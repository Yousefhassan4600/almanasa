<?php

namespace App\Filament\Resources\ProviderPlans\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProviderPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                TextInput::make('currency_code')
                    ->label('Currency Code')
                    ->required()
                    ->maxLength(3),
                TextInput::make('billing_period_days')
                    ->label('Billing Period Days')
                    ->numeric()
                    ->required(),
                TextInput::make('max_students')
                    ->label('Max Students')
                    ->numeric(),
                TextInput::make('max_courses')
                    ->label('Max Courses')
                    ->numeric(),
                TextInput::make('max_teachers')
                    ->label('Max Teachers')
                    ->numeric(),
                KeyValue::make('features')
                    ->label('Features')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active'),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required(),
            ]);
    }
}
