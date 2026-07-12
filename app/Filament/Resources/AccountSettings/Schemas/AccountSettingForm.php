<?php

namespace App\Filament\Resources\AccountSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('primary_color')
                    ->label('Primary Color'),
                TextInput::make('secondary_color')
                    ->label('Secondary Color'),
                Toggle::make('website_enabled')
                    ->label('Website Enabled'),
                Toggle::make('registration_enabled')
                    ->label('Registration Enabled'),
                Toggle::make('chat_enabled')
                    ->label('Chat Enabled'),
                Toggle::make('payment_enabled')
                    ->label('Payment Enabled'),
                TextInput::make('tax_percentage')
                    ->label('Tax Percentage')
                    ->numeric()
                    ->required(),
                TextInput::make('completion_watch_percentage')
                    ->label('Completion Watch Percentage')
                    ->numeric()
                    ->required(),
            ]);
    }
}
