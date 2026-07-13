<?php

namespace App\Http\Controllers;

use App\Actions\Website\ResolveProviderStudentAccount;
use App\Enums\Gender;
use App\Enums\ProviderType;
use App\Livewire\Website\LoginForm;
use App\Models\City;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\Provider;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProviderWebsiteAuthController extends Controller
{
    public function sendOtp(Request $request, string $accountSubdomain): RedirectResponse
    {
        $provider = $this->provider($accountSubdomain);

        $data = $request->validate([
            'dial_country_code' => ['required', 'regex:/^\+[0-9]{1,4}$/'],
            'phone' => ['required', 'regex:/^[0-9]{7,15}$/'],
        ]);

        $phone = (string) preg_replace('/\D+/', '', $data['phone']);

        session()->put(LoginForm::challengeKeyFor($provider->id), [
            'provider_id' => $provider->id,
            'dial_country_code' => $data['dial_country_code'],
            'phone' => $phone,
            'code_hash' => Hash::make((string) config('almanasa.website_otp_code')),
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return redirect('/otp.html');
    }

    public function verifyOtp(
        Request $request,
        string $accountSubdomain,
        ResolveProviderStudentAccount $resolveProviderStudentAccount,
    ): RedirectResponse {
        $provider = $this->provider($accountSubdomain);
        $otp = $request->string('otp')->toString()
            ?: $request->string('otp1')->toString()
                .$request->string('otp2')->toString()
                .$request->string('otp3')->toString()
                .$request->string('otp4')->toString();

        validator(['otp' => $otp], [
            'otp' => ['required', 'digits_between:4,8'],
        ])->validate();

        $challengeKey = LoginForm::challengeKeyFor($provider->id);
        $challenge = session()->get($challengeKey);

        if (! is_array($challenge) || ($challenge['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget($challengeKey);

            throw ValidationException::withMessages([
                'otp' => __('The verification code expired. Request a new code.'),
            ]);
        }

        if (! Hash::check($otp, (string) $challenge['code_hash'])) {
            throw ValidationException::withMessages([
                'otp' => __('The verification code is invalid.'),
            ]);
        }

        $account = $resolveProviderStudentAccount->handle(
            $provider,
            (string) $challenge['dial_country_code'],
            (string) $challenge['phone'],
        );

        Auth::login($account->owner);
        $request->session()->regenerate();
        $request->session()->put('current_account_id', $account->id);
        $request->session()->put('current_provider_id', $provider->id);
        session()->forget($challengeKey);

        $account->owner->loadMissing('studentProfile');

        return redirect($account->owner->studentProfile ? '/index.html' : '/register.html');
    }

    public function completeProfile(Request $request, string $accountSubdomain): RedirectResponse
    {
        $this->provider($accountSubdomain);
        $user = $request->user();

        abort_unless($user, 403);

        if ($user->studentProfile()->exists()) {
            return redirect('/index.html');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique(StudentProfile::class, 'email')],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(array_keys(Gender::options()))],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => [
                'required',
                Rule::exists(City::class, 'id')->where(fn ($query) => $query->where('country_id', $request->integer('country_id'))),
            ],
            'education_stage_id' => ['required', Rule::exists(EducationStage::class, 'id')],
            'grade_id' => [
                'required',
                Rule::exists(Grade::class, 'id')->where(fn ($query) => $query->where('education_stage_id', $request->integer('education_stage_id'))),
            ],
            'school_name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($data, $request, $user): void {
            $avatarPath = $request->hasFile('avatar')
                ? $request->file('avatar')->store('student-avatars', 'public')
                : null;

            $user->forceFill([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
            ])->save();

            StudentProfile::query()->create([
                'user_id' => $user->id,
                'email' => $data['email'],
                'avatar' => $avatarPath,
                'gender' => $data['gender'],
                'country_id' => $data['country_id'],
                'city_id' => $data['city_id'],
                'education_stage_id' => $data['education_stage_id'],
                'grade_id' => $data['grade_id'],
                'school_name' => $data['school_name'],
            ]);
        });

        return redirect('/index.html');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login.html');
    }

    private function provider(string $accountSubdomain): Provider
    {
        return Provider::query()
            ->where('subdomain', $accountSubdomain)
            ->where('is_active', true)
            ->where('website_enabled', true)
            ->whereHas('activeSubscription')
            ->whereIn('type', [
                ProviderType::Academy,
                ProviderType::StandaloneTeacher,
            ])
            ->firstOrFail();
    }
}
