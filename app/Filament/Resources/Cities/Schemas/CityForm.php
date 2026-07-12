<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric()
                    ->required(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
            ]);
    }
}
