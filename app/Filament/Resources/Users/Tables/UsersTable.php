<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class UsersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('name')
                ->label(__('admin.labels.Name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('dial_country_code')
                ->label(__('admin.labels.Dial Country Code'))
                ->searchable()
                ->sortable(),
            TextColumn::make('phone')
                ->label(__('admin.labels.Phone'))
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label(__('admin.labels.Is Active'))
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
