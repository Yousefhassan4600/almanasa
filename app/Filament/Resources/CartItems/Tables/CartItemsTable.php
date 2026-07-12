<?php

namespace App\Filament\Resources\CartItems\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CartItemsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('cart_id')
                ->label('Cart Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('package_id')
                ->label('Package Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('quantity')
                ->label('Quantity')
                ->searchable()
                ->sortable(),
            TextColumn::make('unit_price')
                ->label('Unit Price')
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
