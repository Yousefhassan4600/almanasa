<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                TextInput::make('phone_code')
                    ->label('Phone Code'),
                TextInput::make('currency_code')
                    ->label('Currency Code'),
            ]);
    }
}
