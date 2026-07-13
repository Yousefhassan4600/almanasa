<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AccountsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('owner_user_id')
                ->label('Owner')
                ->state(fn ($record): string => $record->owner?->name ?? '-'),
            IconColumn::make('is_active')
                ->label('Active')
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
