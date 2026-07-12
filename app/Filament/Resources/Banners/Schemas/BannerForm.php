<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                TextInput::make('subtitle')
                    ->label('Subtitle'),
                TextInput::make('image')
                    ->label('Image'),
                TextInput::make('button_text')
                    ->label('Button Text'),
                TextInput::make('button_url')
                    ->label('Button Url'),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
