<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class OrdersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('cart_id')
                ->label('Cart Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('order_number')
                ->label('Order Number')
                ->searchable()
                ->sortable(),
            TextColumn::make('subtotal')
                ->label('Subtotal')
                ->searchable()
                ->sortable(),
            TextColumn::make('tax')
                ->label('Tax')
                ->searchable()
                ->sortable(),
            TextColumn::make('discount')
                ->label('Discount')
                ->searchable()
                ->sortable(),
            TextColumn::make('total')
                ->label('Total')
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
