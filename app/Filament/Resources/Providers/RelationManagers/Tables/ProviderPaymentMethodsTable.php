<?php

namespace App\Filament\Resources\Providers\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ProviderPaymentMethodsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'paymentMethod',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('paymentMethod.name')
                ->label(__('admin.labels.Payment Method'))
                ->searchable()
                ->sortable(),
            TextColumn::make('account_number')
                ->label(__('admin.labels.Account Number'))
                ->searchable(),
            TextColumn::make('account_holder')
                ->label(__('admin.labels.Account Holder'))
                ->searchable(),
            TextColumn::make('phone_number')
                ->label(__('admin.labels.Phone Number'))
                ->searchable(),
            TextColumn::make('phone_holder')
                ->label(__('admin.labels.Phone Holder'))
                ->searchable(),
        ];
    }
}
