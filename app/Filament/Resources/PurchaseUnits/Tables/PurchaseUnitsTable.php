<?php

namespace App\Filament\Resources\PurchaseUnits\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PurchaseUnitsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->wrap(),
            TextColumn::make('type')
                ->label('Type')
                ->badge()
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }
}
