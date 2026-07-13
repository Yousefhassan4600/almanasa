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
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('provider_plan_option_id')
                    ->label('Plan Option')
                    ->relationship('planOption', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => sprintf(
                        '%s - %s days - %s',
                        $record->plan?->name ?? 'Plan',
                        $record->billing_period_days,
                        $record->price,
                    ))
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(ProviderSubscriptionStatus::options())
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('starts_at')
                    ->label('Starts At'),
                DateTimePicker::make('ends_at')
                    ->label('Ends At'),
                KeyValue::make('metadata')
                    ->label('Metadata')
                    ->columnSpanFull(),
            ]);
    }
}
