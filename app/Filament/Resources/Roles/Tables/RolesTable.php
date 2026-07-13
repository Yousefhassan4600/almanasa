<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class RolesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('creator.owner.name')
                ->label('Created By Account')
                ->searchable(),
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
