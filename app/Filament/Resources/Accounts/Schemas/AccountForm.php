<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(AccountType::options())
                    ->required(),
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric(),
                TextInput::make('owner_user_id')
                    ->label('Owner User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('parent_account_id')
                    ->label('Parent Account Id')
                    ->numeric(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                TextInput::make('phone')
                    ->label('Phone'),
                TextInput::make('email')
                    ->label('Email'),
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric(),
                TextInput::make('city_id')
                    ->label('City Id')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(AccountStatus::options())
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label('Approved At'),
            ]);
    }
}
