<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\User;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default(AccountType::Student->value),
                Select::make('owner_user_id')
                    ->label('Student User')
                    ->relationship('owner', 'phone')
                    ->getOptionLabelFromRecordUsing(fn(User $record): string => trim("{$record->name} {$record->phone}"))
                    ->live()
                    ->disabled(true)
                    ->dehydrated()
                    ->rules([
                        fn(Get $get, ?Account $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($value) || blank($providerId)) {
                                return;
                            }

                            $accountExists = Account::query()
                                ->where('owner_user_id', $value)
                                ->where('type', AccountType::Student->value)
                                ->where('provider_id', $providerId)
                                ->when($record?->exists, fn($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($accountExists) {
                                $fail('This user already has a student account for the selected provider.');
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->default(fn(): ?int => self::currentProviderId())
                    ->disabled(true)
                    ->dehydrated()
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label('Approved At')
                    ->default(now())
                    ->readOnly()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    private static function currentProviderId(): ?int
    {
        $account = request()->attributes->get('current_account');

        if ($account instanceof Account && $account->provider_id) {
            return $account->provider_id;
        }

        $providerId = (int) request()->session()->get('current_provider_id');

        return $providerId > 0 ? $providerId : null;
    }
}
