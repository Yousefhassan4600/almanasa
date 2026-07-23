<?php

namespace App\Filament\Support;

use App\Enums\AccountType;
use App\Models\AcademyTeacher;
use App\Models\Account;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class CurrentAccount
{
    public static function account(): ?Account
    {
        $account = request()->attributes->get('current_account');

        if ($account instanceof Account) {
            return $account;
        }

        if (! request()->hasSession()) {
            return null;
        }

        $accountId = (int) request()->session()->get('current_account_id');

        return $accountId > 0 ? Account::query()->find($accountId) : null;
    }

    public static function isSaasOwner(): bool
    {
        $account = self::account();

        return $account?->type === AccountType::SaasOwner;
    }

    public static function isAcademyTeacher(): bool
    {
        $account = self::account();

        return $account?->type === AccountType::AcademyTeacher;
    }

    public static function isStandaloneTeacher(): bool
    {
        $account = self::account();

        return $account?->type === AccountType::StandaloneTeacher;
    }

    public static function academyTeacherId(): ?int
    {
        $account = self::account();

        if (! $account || ! self::isAcademyTeacher()) {
            return null;
        }

        return AcademyTeacher::query()
            ->where('provider_id', $account->provider_id)
            ->where('teacher_account_id', $account->id)
            ->value('id');
    }

    public static function scopeCoursesToAcademyTeacher(Builder $query): Builder
    {
        $account = self::account();

        if (! $account || ! self::isAcademyTeacher()) {
            return $query;
        }

        return $query->whereHas('academyTeacher', fn (Builder $query): Builder => $query
            ->where('teacher_account_id', $account->id));
    }

    public static function scopeCoursesToCurrentAccount(Builder $query): Builder
    {
        if (method_exists($query->getModel(), 'scopeForCurrentTenant')) {
            $query->forCurrentTenant(self::account());
        }

        return self::scopeCoursesToAcademyTeacher($query);
    }

    public static function scopeLessonsToAcademyTeacher(Builder $query): Builder
    {
        $account = self::account();

        if (! $account || ! self::isAcademyTeacher()) {
            return $query;
        }

        return $query->whereHas('course.academyTeacher', fn (Builder $query): Builder => $query
            ->where('teacher_account_id', $account->id));
    }

    public static function scopeLessonsToCurrentAccount(Builder $query): Builder
    {
        if (method_exists($query->getModel(), 'scopeForCurrentTenant')) {
            $query->forCurrentTenant(self::account());
        }

        return self::scopeLessonsToAcademyTeacher($query);
    }

    public static function providerId(): ?int
    {
        $providerId = self::account()?->provider_id
            ?: (int) request()->session()->get('current_provider_id');

        return $providerId > 0 ? $providerId : null;
    }

    public static function providerSelect(Select $select): Select
    {
        return $select
            ->default(fn (): ?int => self::providerId())
            ->disabled(fn (): bool => ! self::isSaasOwner())
            ->dehydrated(true);
    }

    public static function providerTextInput(TextInput $input): TextInput
    {
        return $input
            ->default(fn (): ?int => self::providerId())
            ->disabled(fn (): bool => ! self::isSaasOwner())
            ->dehydrated(true);
    }
}
