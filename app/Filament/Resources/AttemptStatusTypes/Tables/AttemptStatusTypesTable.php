<?php

namespace App\Filament\Resources\AttemptStatusTypes\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AttemptStatusTypesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),
            TextColumn::make('name')
                ->label(__('admin.labels.Name'))
                ->wrap(),
            TextColumn::make('slug')
                ->label(__('admin.labels.Slug'))
                ->badge(),
            IconColumn::make('is_active')
                ->label(__('admin.labels.Is Active'))
                ->boolean(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'id';
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
