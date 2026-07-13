<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\AccountType;
use App\Enums\EmployeeRole;
use App\Models\Account;
use App\Models\Employee;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->label('Provider')
                    ->relationship(
                        name: 'account',
                        titleAttribute: 'id',
                        modifyQueryUsing: fn ($query) => $query
                            ->whereNotNull('provider_id')
                            ->whereIn('type', [
                                AccountType::Academy->value,
                                AccountType::StandaloneTeacher->value,
                            ])
                            ->with('provider')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Account $record): string => $record->provider?->name ?? "Account #{$record->getKey()}")
                    ->live()
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('User')
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
                                $fail('This user is already an employee for the selected provider.');

                                return;
                            }

                            $existingAccount = Account::query()
                                ->where('provider_id', $account->provider_id)
                                ->where('owner_user_id', $value)
                                ->exists();

                            if ($existingAccount) {
                                $fail('This user already has an account for the selected provider.');
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('predefined_role')
                    ->label('Predefined Role')
                    ->options(EmployeeRole::options())
                    ->live()
                    ->requiredWithout('role_id')
                    ->prohibits('role_id'),
                Select::make('role_id')
                    ->label('Custom Role')
                    ->relationship('role', 'name')
                    ->live()
                    ->nullable()
                    ->requiredWithout('predefined_role')
                    ->prohibits('predefined_role')
                    ->preload()
                    ->searchable(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
