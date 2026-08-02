<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\AccountResource;

use App\Models\Account;
use App\Models\User;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
class LoginController extends Controller
{
    private const STUDENT_OTP_CODE = '1234';

    private const ACCOUNT_TYPE = 'student';

    public function login(Request $request)
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
                ->statusCode(404)
                ->toResponse($request);
        }

        $user->otp = self::STUDENT_OTP_CODE;
        $user->save();

        return ApiResponse::make()
            ->message(__('otp_sent_successfully'))
            ->data([
                'role' => self::ACCOUNT_TYPE,
            ])
            ->toResponse($request);
    }

    public function resendCode(Request $request)
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
                ->statusCode(404)
                ->toResponse($request);
        }

        $user->otp = self::STUDENT_OTP_CODE;
        $user->save();

        return ApiResponse::make()
            ->message(__('otp_sent_successfully'))
            ->data([
                'role' => self::ACCOUNT_TYPE,
            ])
            ->toResponse($request);
    }

    public function verify(Request $request)
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
                ->statusCode(401)
                ->toResponse($request);
        }

        $providedOtp = trim((string) $validated['otp_code']);
        $storedOtp = trim((string) ($user->otp ?? ''));

        $isOtpInvalid = $storedOtp === '' || ! hash_equals($storedOtp, $providedOtp);

        if ($isOtpInvalid) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('invalid_or_expired_otp_code'))
                ->statusCode(422)
                ->toResponse($request);
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
            ])
            ->toResponse($request);
    }

    public function studentServices(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $role = $request->attributes->get('auth_role');

        if (! $user) {
            return ApiResponse::make()
                ->success(false)
                ->message(__('unauthenticated'))
                ->statusCode(401)
                ->toResponse($request);
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
            ])
            ->toResponse($request);
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
}
