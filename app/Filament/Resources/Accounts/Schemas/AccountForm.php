<?php

namespace App\Filament\Resources\Accounts\Schemas;

use App\Enums\AccountType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(AccountType::options())
                    ->required(),
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('owner_user_id')
                    ->label('Owner')
                    ->relationship('owner', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label('Approved At')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
