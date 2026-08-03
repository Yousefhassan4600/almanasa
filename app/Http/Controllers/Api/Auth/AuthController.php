<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProfileResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\AccountResource;

use App\Models\Account;
use App\Models\StudentProfile;
use App\Models\User;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
class AuthController extends Controller
{
    private const STUDENT_OTP_CODE = '1234';

    private const ACCOUNT_TYPE = 'student';

    public function login(Request $request) : ApiResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'dial_code' => ['nullable', 'string'],
        ]);

        $user = $this->findActiveUserByPhone([
            'dial_code' => $validated['dial_code'] ?? null,
            'phone' => $this->normalizePhone($validated['phone']),
        ]);

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('student_not_found'))
                ->statusCode(404);
        }

        $user->otp = self::STUDENT_OTP_CODE;
        $user->save();

        return ApiResponse::make()
            ->message(__('otp_sent_successfully'))
            ->data([
                'role' => self::ACCOUNT_TYPE,
            ]);
    }

    public function resendCode(Request $request) : ApiResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'dial_code' => ['nullable', 'string'],
        ]);

        $user = $this->findActiveUserByPhone([
            'dial_code' => $validated['dial_code'] ?? null,
            'phone' => $this->normalizePhone($validated['phone']),
        ]);

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('student_not_found'))
                ->statusCode(404);
        }

        $user->otp = self::STUDENT_OTP_CODE;
        $user->save();

        return ApiResponse::make()
            ->message(__('otp_sent_successfully'))
            ->data([
                'role' => self::ACCOUNT_TYPE,
            ]);
    }

    public function verify(Request $request) : ApiResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'dial_code' => ['nullable', 'string'],
            'otp_code' => ['required', 'string'],
        ]);

        $user = $this->findActiveUserByPhone([
            'dial_code' => $validated['dial_code'] ?? null,
            'phone' => $this->normalizePhone($validated['phone']),
        ]);

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('invalid_credentials'))
                ->statusCode(401);
        }

        $providedOtp = trim((string) $validated['otp_code']);
        $storedOtp = trim((string) ($user->otp ?? ''));

        $isOtpInvalid = $storedOtp === '' || ! hash_equals($storedOtp, $providedOtp);

        if ($isOtpInvalid) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('invalid_or_expired_otp_code'))
                ->statusCode(422);
        }

        $user->tokens()->delete();
        $token = $user->createToken("otp_{$user->phone}")->plainTextToken;
        $user->verified_at = Carbon::now();
        $user->otp = null;
        $user->save();

        return ApiResponse::make()
            ->message(__('login_successful'))
            ->data([
                'role' => self::ACCOUNT_TYPE,
                'token' => $token,
                'user' => UserResource::make($user),
            ]);
    }

    public function studentServices(Request $request) : ApiResponse
    {
        $user = $request->attributes->get('auth_user');
        $role = $request->attributes->get('auth_role');

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('unauthenticated'))
                ->statusCode(401);
        }
        $accounts = Account::query()
        ->where('owner_user_id', $user->id)
        ->where('type', self::ACCOUNT_TYPE)
        ->get();

        return ApiResponse::make()
            ->message(__('student_services_retrieved'))
            ->data([
                'role' => $role,
                'accounts' => AccountResource::collection($accounts),
            ]);
    }

      public function me(Request $request) : ApiResponse
    {
        $user = $request->attributes->get('auth_user');
        $role = $request->attributes->get('auth_role');

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('unauthenticated'))
                ->statusCode(401);
        }
        $studentProfile = StudentProfile::query()
        ->where('user_id', $user->id)
        ->first();

        return ApiResponse::make()
            ->message(__('student_services_retrieved'))
            ->data([
                'role' => $role,
                'student_profile' => $studentProfile ? StudentProfileResource::make($studentProfile) : null,
            ]);
    }

    public function editProfile(Request $request) : ApiResponse
{
    $user = $request->attributes->get('auth_user');

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('unauthenticated'))
                ->statusCode(401);
        }

 $validated = $request->validate([
    'first_name'        => ['required_without_all:last_name,email,avatar,country_id,city_id,education_stage_id,grade_id,school_name', 'nullable', 'string', 'max:255'],
    'last_name'         => ['required_without_all:first_name,email,avatar,country_id,city_id,education_stage_id,grade_id,school_name', 'nullable', 'string', 'max:255'],
    'email'             => ['required_without_all:first_name,last_name,avatar,country_id,city_id,education_stage_id,grade_id,school_name', 'nullable', 'email', 'max:255'],
    'avatar'            => ['required_without_all:first_name,last_name,email,country_id,city_id,education_stage_id,grade_id,school_name', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    'country_id'        => ['required_without_all:first_name,last_name,email,avatar,city_id,education_stage_id,grade_id,school_name', 'nullable', 'integer', 'exists:countries,id'],
    'city_id'           => ['required_without_all:first_name,last_name,email,avatar,country_id,education_stage_id,grade_id,school_name', 'nullable', 'integer', 'exists:cities,id'],
    'education_stage_id'=> ['required_without_all:first_name,last_name,email,avatar,country_id,city_id,grade_id,school_name', 'nullable', 'integer', 'exists:education_stages,id'],
    'grade_id'          => ['required_without_all:first_name,last_name,email,avatar,country_id,city_id,education_stage_id,school_name', 'nullable', 'integer', 'exists:grades,id'],
    'school_name'       => ['required_without_all:first_name,last_name,email,avatar,country_id,city_id,education_stage_id,grade_id', 'nullable', 'string', 'max:255'],
]);


      $student = StudentProfile::query()
        ->where('user_id', $user->id)
        ->first();
    $userUpdates = [];

    if ($request->exists('first_name')) {
        $userUpdates['first_name'] = $validated['first_name'];
    }

    if ($request->exists('last_name')) {
        $userUpdates['last_name'] = $validated['last_name'];
    }



    if ($userUpdates) {
        $student->user->update($userUpdates);
    }

    $updates = [];

    if ($request->exists('email')) {
        $updates['email'] = $validated['email'];
    }

    if ($request->exists('country_id')) {
        $updates['country_id'] = $validated['country_id'];
    }

    if ($request->exists('city_id')) {
        $updates['city_id'] = $validated['city_id'];
    }

    if ($request->exists('education_stage_id')) {
        $updates['education_stage_id'] = $validated['education_stage_id'];
    }

    if ($request->exists('grade_id')) {
        $updates['grade_id'] = $validated['grade_id'];
    }

    if ($request->exists('school_name')) {
        $updates['school_name'] = $validated['school_name'];
    }

    if ($request->hasFile('avatar')) {
        $this->deleteStoredFile($student->avatar);

        $updates['avatar'] = $request->file('avatar')
            ->store('students/avatars', 'public');
    }

    if ($updates) {
        $student->update($updates);
    }

    $student->load('user', 'country', 'city', 'education_stage', 'grade');

    return ApiResponse::make()
        ->message(__('profile_updated_successfully'))
        ->data([
            'role' => 'student',
            'user' => StudentProfileResource::make($student),
        ]);
}

    public function logout(Request $request): ApiResponse
    {
        /** @var User $user */
        $user = $request->attributes->get('auth_user');

        $user->currentAccessToken()?->delete();

        return ApiResponse::make()
            ->message(__('logout_successful'));
    }
    private function findActiveUserByPhone(array $validated): ?User
    {
        $query = User::query()
            ->where('phone', $validated['phone'])
            ->whereHas('ownedAccounts', function ($q) {
                $q->where('type', self::ACCOUNT_TYPE)
                  ->where('is_active', true);
            });

        if (! empty($validated['dial_code'])) {
            $query->where('dial_country_code', $validated['dial_code']);
        }

        return $query->latest('id')->first();
    }

    private function normalizePhone(string $phone): string
    {
        return ltrim(preg_replace('/\s+/', '', $phone), '0');
    }

      private function deleteStoredFile(?string $path): void
    {
        if (! $path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
