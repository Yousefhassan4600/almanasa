<?php

namespace App\Filament\Resources\ProviderSubscriptions\Schemas;

use App\Enums\ProviderSubscriptionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProviderSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('provider_plan_id')
                    ->label('Provider Plan Id')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(ProviderSubscriptionStatus::options())
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),
                TextInput::make('currency_code')
                    ->label('Currency Code')
                    ->required()
                    ->maxLength(3),
                DateTimePicker::make('trial_ends_at')
                    ->label('Trial Ends At'),
                DateTimePicker::make('starts_at')
                    ->label('Starts At'),
                DateTimePicker::make('ends_at')
                    ->label('Ends At'),
                DateTimePicker::make('cancelled_at')
                    ->label('Cancelled At'),
                KeyValue::make('metadata')
                    ->label('Metadata')
                    ->columnSpanFull(),
            ]);
    }
}
