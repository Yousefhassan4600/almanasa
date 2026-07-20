<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;

class NotificationsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('user_id')
                ->label(__('admin.labels.User Id'))
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label(__('admin.labels.Provider Id'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->sortable(),
            TextColumn::make('read_at')
                ->label(__('admin.labels.Read At'))
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
