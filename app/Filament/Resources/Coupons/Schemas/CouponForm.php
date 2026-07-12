<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                TextInput::make('discount_type')
                    ->label('Discount Type')
                    ->required(),
                TextInput::make('value')
                    ->label('Value')
                    ->numeric()
                    ->required(),
                TextInput::make('usage_limit')
                    ->label('Usage Limit')
                    ->numeric(),
                TextInput::make('usage_limit_per_user')
                    ->label('Usage Limit Per User')
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->label('Starts At'),
                DateTimePicker::make('ends_at')
                    ->label('Ends At'),
                Toggle::make('is_active')
                    ->label('Is Active'),
            ]);
    }
}
