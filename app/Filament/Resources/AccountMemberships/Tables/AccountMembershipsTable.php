<?php

namespace App\Filament\Resources\AccountMemberships\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class AccountMembershipsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('user_id')
                ->label('User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('predefined_role')
                ->label('Predefined Role')
                ->searchable()
                ->sortable(),
            TextColumn::make('role_id')
                ->label('Custom Role Id')
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
