<?php

namespace App\Filament\Resources\Tracks\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class TracksTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->searchable()
                ->sortable(),
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

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
