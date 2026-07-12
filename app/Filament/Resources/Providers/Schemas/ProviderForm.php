<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\ProviderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(ProviderType::options())
                    ->required(),
                TextInput::make('owner_user_id')
                    ->label('Owner User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required(),
                TextInput::make('subdomain')
                    ->label('Subdomain'),
                TextInput::make('custom_domain')
                    ->label('Custom Domain'),
                TextInput::make('logo')
                    ->label('Logo'),
                TextInput::make('cover_image')
                    ->label('Cover Image'),
                Textarea::make('bio')
                    ->label('Bio')
                    ->columnSpanFull(),
                TextInput::make('country_id')
                    ->label('Country Id')
                    ->numeric(),
                TextInput::make('city_id')
                    ->label('City Id')
                    ->numeric(),
                Textarea::make('address')
                    ->label('Address')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric(),
            ]);
    }
}
