<?php

namespace App\Actions\StudentPortal\Auth;

use App\Actions\StudentPortal\ResolveProviderStudentAccount;
use App\Models\Account;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ManageWebsiteOtp
{
    public function __construct(private ResolveProviderStudentAccount $resolveProviderStudentAccount) {}

    /**
     * @return array{dialCountryCode: string, phone: string}
     */
    public function send(int $providerId, string $dialCountryCode, string $phone, string $ip): array
    {
        $phone = $this->normalizePhone($phone);
        $rateKey = $this->rateKey('send', $providerId, $dialCountryCode, $phone, $ip);

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            throw ValidationException::withMessages([
                'phone' => __('Too many attempts. Please try again shortly.'),
            ]);
        }

        RateLimiter::hit($rateKey, 60);

        session()->put($this->challengeKey($providerId), [
            'provider_id' => $providerId,
            'dial_country_code' => $dialCountryCode,
            'phone' => $phone,
            'code_hash' => Hash::make((string) config('almanasa.website_otp_code')),
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return [
            'dialCountryCode' => $dialCountryCode,
            'phone' => $phone,
        ];
    }

    public function verify(Provider $provider, string $otp, string $ip): Account
    {
        $challengeKey = $this->challengeKey($provider->id);
        $challenge = session()->get($challengeKey);

        if (! is_array($challenge) || ($challenge['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget($challengeKey);

            throw ValidationException::withMessages([
                'otp' => __('The verification code expired. Request a new code.'),
            ]);
        }

        $dialCountryCode = (string) $challenge['dial_country_code'];
        $phone = (string) $challenge['phone'];
        $rateKey = $this->rateKey('verify', $provider->id, $dialCountryCode, $phone, $ip);

        if (RateLimiter::tooManyAttempts($rateKey, 5) || ! Hash::check($otp, (string) $challenge['code_hash'])) {
            RateLimiter::hit($rateKey, 60);

            throw ValidationException::withMessages([
                'otp' => __('The verification code is invalid.'),
            ]);
        }

        $account = $this->resolveProviderStudentAccount->handle($provider, $dialCountryCode, $phone);

        session()->forget($challengeKey);

        return $account;
    }

    public function challengeKey(int $providerId): string
    {
        return 'website_auth_challenge_'.$providerId;
    }

    private function rateKey(string $action, int $providerId, string $dialCountryCode, string $phone, string $ip): string
    {
        return 'website-auth:'.$action.':'.$providerId.':'.$dialCountryCode.':'.$phone.':'.$ip;
    }

    private function normalizePhone(string $phone): string
    {
        return (string) preg_replace('/\D+/', '', $phone);
    }
}
