<?php

namespace App\Filament\Resources\AccountSettings\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AccountSettingsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('primary_color')
                ->label('Primary Color')
                ->searchable()
                ->sortable(),
            TextColumn::make('secondary_color')
                ->label('Secondary Color')
                ->searchable()
                ->sortable(),
            IconColumn::make('website_enabled')
                ->label('Website Enabled')
                ->boolean(),
            IconColumn::make('registration_enabled')
                ->label('Registration Enabled')
                ->boolean(),
            IconColumn::make('chat_enabled')
                ->label('Chat Enabled')
                ->boolean(),
            IconColumn::make('payment_enabled')
                ->label('Payment Enabled')
                ->boolean(),
            TextColumn::make('tax_percentage')
                ->label('Tax Percentage')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
