<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class EmployeesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('account.provider.name')
                ->label(__('admin.Provider'))
                ->searchable()
                ->sortable(),
            TextColumn::make('user.name')
                ->label(__('admin.User'))
                ->searchable()
                ->sortable(),
            TextColumn::make('role.name')
                ->label(__('admin.Role'))
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label(__('admin.Is Active'))
                ->boolean()
                ->sortable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
