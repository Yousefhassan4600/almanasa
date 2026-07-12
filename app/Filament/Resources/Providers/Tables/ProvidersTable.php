<?php

namespace App\Filament\Resources\Providers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvidersTable extends BaseTable
{
    public static function configure(Table $table): Table
    {
        return parent::configure($table);
    }

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
            TextColumn::make('currentSubscription.status')
                ->label('Subscription')
                ->searchable(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
