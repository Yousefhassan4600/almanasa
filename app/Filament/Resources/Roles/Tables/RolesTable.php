<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;

class RolesTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'provider',
            'creator.owner',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label(__('admin.labels.Name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('creator.owner.name')
                ->label(__('admin.labels.Created By Account'))
                ->searchable(),
            TextColumn::make('guard_name')
                ->label(__('admin.labels.Guard Name'))
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
