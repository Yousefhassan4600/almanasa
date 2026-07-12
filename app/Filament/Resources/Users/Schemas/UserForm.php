<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Gender;
use App\Enums\UserStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Last Name'),
                TextInput::make('email')
                    ->label('Email'),
                TextInput::make('phone')
                    ->label('Phone')
                    ->required(),
                TextInput::make('dial_country_code')
                    ->label('Dial Country Code'),
                Select::make('gender')
                    ->label('Gender')
                    ->options(Gender::options()),
                Select::make('status')
                    ->label('Status')
                    ->options(UserStatus::options())
                    ->required(),
            ]);
    }
}
