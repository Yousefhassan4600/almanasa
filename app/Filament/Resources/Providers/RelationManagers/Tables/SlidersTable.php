<?php

namespace App\Filament\Resources\Providers\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class SlidersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            ImageColumn::make('cover')
                ->label('Cover'),
            TextColumn::make('title')
                ->label('Title')
                ->wrap(),
            TextColumn::make('subtitle')
                ->label('Subtitle')
                ->wrap(),
            ToggleColumn::make('is_active')
                ->label('Is Active'),
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
