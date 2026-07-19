<?php

namespace App\Filament\Resources\PurchaseUnits\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class PurchaseUnitsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->wrap(),
            TextColumn::make('type')
                ->label('Type')
                ->badge(),
            TextColumn::make('period_days')
                ->label('Period Days')
                ->placeholder('-')
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label('Active'),
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

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return false;
    }
}
