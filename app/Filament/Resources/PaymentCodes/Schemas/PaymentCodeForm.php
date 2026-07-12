<?php

namespace App\Filament\Resources\PaymentCodes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric(),
                TextInput::make('duration_days')
                    ->label('Duration Days')
                    ->numeric(),
                TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->numeric()
                    ->required(),
                TextInput::make('used_count')
                    ->label('Used Count')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->label('Expires At'),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
