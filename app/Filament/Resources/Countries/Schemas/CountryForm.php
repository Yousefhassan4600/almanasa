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
                TextInput::make('name.ar')
                    ->label(__('admin.labels.Name (Arabic)'))
                    ->required(),
                TextInput::make('name.en')
                    ->label(__('admin.labels.Name (English)'))
                    ->required(),
                TextInput::make('code')
                    ->label(__('admin.labels.Code')),
                TextInput::make('phone_code')
                    ->label(__('admin.labels.Phone Code')),
            ]);
    }
}
