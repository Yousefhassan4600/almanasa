<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\AccountType;
use App\Filament\Support\CurrentAccount;
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
                    ->label(__('admin.labels.Student User'))
                    ->relationship('owner', 'phone')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => trim("{$record->name} {$record->phone}"))
                    ->live()
                    ->disabled(true)
                    ->dehydrated()
                    ->rules([
                        fn (Get $get, ?Account $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                            $providerId = $get('provider_id');

                            if (blank($value) || blank($providerId)) {
                                return;
                            }

                            $accountExists = Account::query()
                                ->where('owner_user_id', $value)
                                ->where('type', AccountType::Student->value)
                                ->where('provider_id', $providerId)
                                ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($accountExists) {
                                $fail(__('admin.messages.student_account_already_exists'));
                            }
                        },
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                CurrentAccount::providerSelect(Select::make('provider_id'))
                    ->label(__('admin.labels.Provider'))
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label(__('admin.labels.Approved At'))
                    ->default(now())
                    ->readOnly()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('admin.labels.Is Active'))
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
