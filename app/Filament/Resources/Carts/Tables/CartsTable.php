<?php

namespace App\Filament\Resources\Carts\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CartsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
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
