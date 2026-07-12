<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class AccountsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('owner_user_id')
                ->label('Owner User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('slug')
                ->label('Slug')
                ->searchable()
                ->sortable(),
            TextColumn::make('subdomain')
                ->label('Subdomain')
                ->searchable()
                ->sortable(),
            TextColumn::make('custom_domain')
                ->label('Custom Domain')
                ->searchable()
                ->sortable(),
            TextColumn::make('logo')
                ->label('Logo')
                ->searchable()
                ->sortable(),
            TextColumn::make('cover_image')
                ->label('Cover Image')
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
