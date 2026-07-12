<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class RolesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('guard_name')
                ->label('Guard Name')
                ->searchable()
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
