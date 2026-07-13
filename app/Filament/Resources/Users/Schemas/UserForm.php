<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                TextInput::make('dial_country_code')
                    ->label('Dial Country Code')
                    ->default('+20'),
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->saved(fn (?string $state): bool => filled($state)),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
