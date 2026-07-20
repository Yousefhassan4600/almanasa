<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;

class SupportTicketsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label(__('admin.labels.Provider Id'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable()
                ->sortable(),
            TextColumn::make('user_id')
                ->label(__('admin.labels.User Id'))
                ->searchable()
                ->sortable(),
            TextColumn::make('subject')
                ->label(__('admin.labels.Subject'))
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label(__('admin.labels.Status'))
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
