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
                ->label('#')
                ->sortable(),
            TextColumn::make('account.provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            TextColumn::make('role.name')
                ->label('Role')
                ->formatStateUsing(fn($state, $record): string => $state ?? $record->predefined_role)
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
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
