<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CitiesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),
            TextColumn::make('name')
                ->label(__('admin.labels.Name')),
            TextColumn::make('country.name')
                ->label(__('admin.labels.Country')),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
