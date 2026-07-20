<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\AccountType;
use App\Filament\Support\CurrentAccount;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Provider;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CurrentAccount::providerSelect(Select::make('provider_id'))
                    ->label(__('admin.Provider'))
                    ->options(fn (): array => Provider::query()
                        ->whereHas('accounts', fn ($query) => $query
                            ->whereIn('type', [
                                AccountType::Academy->value,
                                AccountType::StandaloneTeacher->value,
                            ]))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->afterStateHydrated(function (Select $component, ?Employee $record): void {
                        $component->state($record?->account?->provider_id ?? CurrentAccount::providerId());
                    })
                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                        $set('account_id', self::ownerAccountIdForProvider($state));
                        $set('role_id', null);
                    })
                    ->live()
                    ->preload()
                    ->searchable()
                    ->required(),
                Hidden::make('account_id')
                    ->default(fn (Get $get): ?int => self::ownerAccountIdForProvider($get('provider_id')))
                    ->dehydrateStateUsing(fn (mixed $state, Get $get): ?int => self::ownerAccountIdForProvider($get('provider_id')))
                    ->dehydrated(true),
                Select::make('user_id')
                    ->label(__('admin.User'))
                    ->relationship('user', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                    ->live()
                    ->rules([
                        fn (Get $get, ?Employee $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $accountId = $get('account_id');

                            if (blank($accountId) || blank($value)) {
                                return;
                            }

                            $account = Account::query()->find($accountId);

                            if (! $account?->provider_id) {
                                return;
                            }

                            $existingEmployee = Employee::query()
                                ->where('account_id', $account->id)
                                ->where('user_id', $value)
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($existingEmployee) {
                                $fail(__('admin.messages.employee_already_exists'));

                                return;
                            }

                            $existingAccount = Account::query()
                                ->where('provider_id', $account->provider_id)
                                ->where('owner_user_id', $value)
                                ->exists();

                            if ($existingAccount) {
                                $fail(__('admin.messages.user_already_has_provider_account'));
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('role_id')
                    ->label(__('admin.Custom Role'))
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query, Get $get) => $query
                            ->where('is_assignable', true)
                            ->when(
                                $get('provider_id'),
                                fn ($query, int $providerId) => $query->where('provider_id', $providerId),
                                fn ($query) => $query->whereRaw('1 = 0'),
                            )
                    )
                    ->live()
                    ->disabled(fn (Get $get): bool => blank($get('provider_id')))
                    ->required()
                    ->preload()
                    ->searchable(),
                Toggle::make('is_active')
                    ->label(__('admin.Is Active'))
                    ->default(true),
            ]);
    }

    private static function ownerAccountIdForProvider(mixed $providerId): ?int
    {
        if (blank($providerId)) {
            return null;
        }

        return Account::query()
            ->where('provider_id', $providerId)
            ->whereIn('type', [
                AccountType::Academy->value,
                AccountType::StandaloneTeacher->value,
            ])
            ->oldest('id')
            ->value('id');
    }
}
