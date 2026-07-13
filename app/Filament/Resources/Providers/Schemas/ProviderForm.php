<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\ProviderType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(ProviderType::options())
                            ->required(),
                        Select::make('owner_user_id')
                            ->label('Owner User Id')
                            ->relationship('owner', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required(),
                        Select::make('country_id')
                            ->label('Country Id')
                            ->relationship('country', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('city_id')
                            ->label('City Id')
                            ->relationship('city', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('subdomain')
                            ->label('Subdomain'),
                        Toggle::make('use_custom_domain')
                            ->label('Use Custom Domain')
                            ->reactive(),
                        TextInput::make('custom_domain')
                            ->label('Custom Domain')
                            ->visible(fn ($get): bool => $get('use_custom_domain')),
                    ]),
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('bio')
                            ->label('Bio')
                            ->columnSpanFull(),
                        TextInput::make('logo')
                            ->label('Logo'),
                        TextInput::make('cover_image')
                            ->label('Cover Image'),
                        Textarea::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Is Active'),
                    ]),
                Section::make('Settings')
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label('Primary Color'),
                        ColorPicker::make('secondary_color')
                            ->label('Secondary Color'),
                        TextInput::make('completion_watch_percentage')
                            ->label('Completion Watch Percentage')
                            ->numeric()
                            ->default(70),
                        Section::make('Features')
                            ->schema([
                                Toggle::make('website_enabled')
                                    ->label('Website Enabled')
                                    ->default(true),
                                Toggle::make('registration_enabled')
                                    ->label('Registration Enabled')
                                    ->default(true),
                                Toggle::make('chat_enabled')
                                    ->label('Chat Enabled')
                                    ->default(true),
                                Toggle::make('payment_enabled')
                                    ->label('Payment Enabled')
                                    ->default(true),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
            ]);
    }
}
