<?php

namespace App\Actions\Website;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResolveProviderStudentAccount
{
    public function handle(Provider $provider, string $dialCountryCode, string $phone): Account
    {
        return DB::transaction(function () use ($provider, $dialCountryCode, $phone): Account {
            $user = User::withTrashed()
                ->where('dial_country_code', $dialCountryCode)
                ->where('phone', $phone)
                ->lockForUpdate()
                ->first();

            if ($user && ($user->trashed() || ! $user->is_active)) {
                throw ValidationException::withMessages([
                    'phone' => __('This account cannot be used. Please contact support.'),
                ]);
            }

            if (! $user) {
                $this->ensureRegistrationIsOpen($provider);

                $user = User::query()->create([
                    'first_name' => null,
                    'last_name' => null,
                    'dial_country_code' => $dialCountryCode,
                    'phone' => $phone,
                    'password' => null,
                    'verified_at' => now(),
                    'is_active' => true,
                ]);
            } elseif (! $user->verified_at) {
                $user->forceFill(['verified_at' => now()])->save();
            }

            $account = Account::withTrashed()
                ->where('provider_id', $provider->id)
                ->where('owner_user_id', $user->id)
                ->where('type', AccountType::Student)
                ->lockForUpdate()
                ->first();

            if ($account && ($account->trashed() || ! $account->is_active)) {
                throw ValidationException::withMessages([
                    'phone' => __('Your student account for this provider is not active.'),
                ]);
            }

            if ($account) {
                return $account;
            }

            $this->ensureRegistrationIsOpen($provider);

            return Account::query()->create([
                'provider_id' => $provider->id,
                'owner_user_id' => $user->id,
                'type' => AccountType::Student,
                'is_active' => true,
                'approved_at' => now(),
            ]);
        });
    }

    private function ensureRegistrationIsOpen(Provider $provider): void
    {
        if ($provider->registration_enabled) {
            return;
        }

        throw ValidationException::withMessages([
            'phone' => __('Registration is currently closed for this provider.'),
        ]);
    }
}
