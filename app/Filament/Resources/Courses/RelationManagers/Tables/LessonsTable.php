<?php

namespace App\Filament\Resources\Courses\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->wrap(),
            TextColumn::make('coursePeriod.name')
                ->label('Period')
                ->searchable(),
            TextColumn::make('items_count')
                ->label('Items')
                ->counts('items')
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
}
