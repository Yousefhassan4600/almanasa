<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class PaymentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('order_id')
                ->label('Order Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('method')
                ->label('Method')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            TextColumn::make('amount')
                ->label('Amount')
                ->searchable()
                ->sortable(),
            TextColumn::make('transaction_reference')
                ->label('Transaction Reference')
                ->searchable()
                ->sortable(),
            TextColumn::make('payment_code')
                ->label('Payment Code')
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
