<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class SupportTicketsTable extends BaseTable
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
            TextColumn::make('subject')
                ->label('Subject')
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
