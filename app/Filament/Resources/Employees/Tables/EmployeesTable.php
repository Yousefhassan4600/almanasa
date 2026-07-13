<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class EmployeesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('user.phone')
                ->label('User Phone')
                ->searchable()
                ->sortable(),
            TextColumn::make('predefined_role')
                ->label('Predefined Role')
                ->searchable()
                ->sortable(),
            TextColumn::make('role.name')
                ->label('Custom Role')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
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
