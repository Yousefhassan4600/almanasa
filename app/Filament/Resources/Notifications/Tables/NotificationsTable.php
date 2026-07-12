<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class NotificationsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('user_id')
                ->label('User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('read_at')
                ->label('Read At')
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
