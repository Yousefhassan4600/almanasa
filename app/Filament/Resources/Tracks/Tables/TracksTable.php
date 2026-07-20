<?php

namespace App\Filament\Resources\Tracks\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class TracksTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),

            TextColumn::make('name')
                ->label(__('admin.labels.Name')),

            TextColumn::make('code')
                ->label(__('admin.labels.Code')),
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
