<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\ProviderSubscriptionStatus;
use App\Enums\ProviderType;
use App\Models\Account;
use App\Models\City;
use App\Models\Country;
use App\Models\Provider;
use App\Models\ProviderPlan;
use App\Models\ProviderPlanOption;
use App\Models\ProviderSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;

abstract class BaseSeeder extends Seeder
{
    /**
     * @return array{en: string, ar: string}
     */
    protected function translation(string $en, string $ar): array
    {
        return [
            'en' => $en,
            'ar' => $ar,
        ];
    }

    protected function user(string $phone, string $firstName, string $lastName): User
    {
        return User::query()->firstOrCreate([
            'phone' => $phone,
        ], [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dial_country_code' => '+20',
            'password' => bcrypt('123456'),
            'verified_at' => now(),
            'is_active' => true,
        ]);
    }

    protected function provider(
        ProviderType $type,
        User $owner,
        string $slug,
        string $name,
        Country $country,
        City $city,
        ?string $subdomain = null,
    ): Provider {
        return Provider::query()->firstOrCreate([
            'slug' => $slug,
        ], [
            'type' => $type,
            'owner_user_id' => $owner->id,
            'name' => $name,
            'subdomain' => $subdomain,
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);
    }

    protected function providerPlanOption(
        ProviderPlan $plan,
        int $billingPeriodDays,
        int $price,
        int $sortOrder = 0,
    ): ProviderPlanOption {
        return ProviderPlanOption::query()->firstOrCreate([
            'provider_plan_id' => $plan->id,
            'billing_period_days' => $billingPeriodDays,
        ], [
            'price' => $price,
            'sort_order' => $sortOrder,
        ]);
    }

    protected function providerSubscription(Provider $provider, ProviderPlanOption $option): ProviderSubscription
    {
        return ProviderSubscription::query()->firstOrCreate([
            'provider_id' => $provider->id,
            'provider_plan_option_id' => $option->id,
        ], [
            'status' => ProviderSubscriptionStatus::Active,
            'amount' => $option->price,
            'starts_at' => now(),
            'ends_at' => now()->addDays($option->billing_period_days),
        ]);
    }

    protected function account(
        AccountType $type,
        User $owner,
        ?Provider $provider = null,
    ): Account {
        return Account::query()->firstOrCreate([
            'provider_id' => $provider?->id,
            'type' => $type,
            'owner_user_id' => $owner->id,
        ], [
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }
}
