<?php

namespace App\Filament\Resources\Countries\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CountriesTable extends BaseTable
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
            TextColumn::make('phone_code')
                ->label(__('admin.labels.Phone Code')),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
