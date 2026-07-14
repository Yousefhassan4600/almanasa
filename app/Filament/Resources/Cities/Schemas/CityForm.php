<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('name.ar')
                    ->label('Name (Arabic)')
                    ->required(),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required(),
            ]);
    }
}
