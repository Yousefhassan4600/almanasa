<?php

namespace App\Livewire\Website;

use App\Actions\Website\ResolveProviderStudentAccount;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LoginForm extends Component
{
    #[Locked]
    public int $providerId;

    public string $dialCountryCode = '+20';

    public string $phone = '';

    public string $otp = '';

    public string $otp1 = '';

    public string $otp2 = '';

    public string $otp3 = '';

    public string $otp4 = '';

    public bool $otpSent = false;

    public function mount(int $providerId): void
    {
        $this->providerId = $providerId;
        $this->otpSent = session()->has($this->challengeKey());
    }

    public function sendOtp(): mixed
    {
        $data = $this->validate([
            'dialCountryCode' => ['required', 'regex:/^\+[0-9]{1,4}$/'],
            'phone' => ['required', 'regex:/^[0-9]{7,15}$/'],
        ]);

        $this->phone = $this->normalizePhone($data['phone']);
        $this->dialCountryCode = $data['dialCountryCode'];

        $rateKey = $this->rateKey('send');

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            throw ValidationException::withMessages([
                'phone' => __('Too many attempts. Please try again shortly.'),
            ]);
        }

        RateLimiter::hit($rateKey, 60);

        session()->put($this->challengeKey(), [
            'provider_id' => $this->providerId,
            'dial_country_code' => $this->dialCountryCode,
            'phone' => $this->phone,
            'code_hash' => Hash::make((string) config('almanasa.website_otp_code')),
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        $this->otpSent = true;
        $this->resetOtpFields();

        return $this->redirect('/otp.html', navigate: false);
    }

    public function verify(ResolveProviderStudentAccount $resolveProviderStudentAccount): mixed
    {
        $this->otp = $this->otp !== '' ? $this->otp : $this->otp1.$this->otp2.$this->otp3.$this->otp4;

        $this->validate([
            'otp' => ['required', 'digits_between:4,8'],
        ]);

        $challenge = session()->get($this->challengeKey());

        if (! is_array($challenge) || ($challenge['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget($this->challengeKey());

            throw ValidationException::withMessages([
                'otp' => __('The verification code expired. Request a new code.'),
            ]);
        }

        $rateKey = $this->rateKey('verify');

        if (RateLimiter::tooManyAttempts($rateKey, 5) || ! Hash::check($this->otp, (string) $challenge['code_hash'])) {
            RateLimiter::hit($rateKey, 60);

            throw ValidationException::withMessages([
                'otp' => __('The verification code is invalid.'),
            ]);
        }

        $provider = $this->provider();
        $account = $resolveProviderStudentAccount->handle(
            $provider,
            (string) $challenge['dial_country_code'],
            (string) $challenge['phone'],
        );

        Auth::login($account->owner);
        session()->regenerate();
        session()->put('current_account_id', $account->id);
        session()->put('current_provider_id', $provider->id);
        session()->forget($this->challengeKey());

        $account->owner->loadMissing('studentProfile');

        if (! $account->owner->studentProfile) {
            return $this->redirect('/register.html', navigate: false);
        }

        return $this->redirect('/index.html', navigate: false);
    }

    public function resetChallenge(): void
    {
        session()->forget($this->challengeKey());
        $this->otpSent = false;
        $this->resetOtpFields();
    }

    public function render(): mixed
    {
        return view('livewire.website.login-form', [
            'provider' => $this->provider(),
            'themeColor' => $this->themeColor(),
            'developmentOtp' => config('almanasa.website_otp_code'),
        ]);
    }

    public static function challengeKeyFor(int $providerId): string
    {
        return 'website_auth_challenge_'.$providerId;
    }

    private function provider(): Provider
    {
        return Provider::query()->findOrFail($this->providerId);
    }

    private function challengeKey(): string
    {
        return self::challengeKeyFor($this->providerId);
    }

    private function rateKey(string $action): string
    {
        return 'website-auth:'.$action.':'.$this->providerId.':'.$this->dialCountryCode.':'.$this->phone.':'.request()->ip();
    }

    private function normalizePhone(string $phone): string
    {
        return (string) preg_replace('/\D+/', '', $phone);
    }

    private function resetOtpFields(): void
    {
        $this->reset('otp', 'otp1', 'otp2', 'otp3', 'otp4');
    }

    private function themeColor(): string
    {
        return $this->provider()->type->value === 'standalone_teacher' ? '#FEB008' : '#5D3FD3';
    }
}
