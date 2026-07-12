<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class AuditLogsTable extends BaseTable
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
            TextColumn::make('action')
                ->label('Action')
                ->searchable()
                ->sortable(),
            TextColumn::make('auditable_type')
                ->label('Auditable Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('auditable_id')
                ->label('Auditable Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('ip_address')
                ->label('Ip Address')
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
