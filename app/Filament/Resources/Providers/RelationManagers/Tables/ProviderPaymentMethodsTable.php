<?php

namespace App\Filament\Resources\Providers\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ProviderPaymentMethodsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('paymentMethod.name')
                ->label('Payment Method')
                ->searchable()
                ->sortable(),
            TextColumn::make('account_number')
                ->label('Account Number')
                ->searchable(),
            TextColumn::make('account_holder')
                ->label('Account Holder')
                ->searchable(),
            TextColumn::make('phone_number')
                ->label('Phone Number')
                ->searchable(),
            TextColumn::make('phone_holder')
                ->label('Phone Holder')
                ->searchable(),
        ];
    }
}
