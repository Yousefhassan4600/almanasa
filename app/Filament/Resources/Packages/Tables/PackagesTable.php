<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PackagesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('account_id')
                ->label('Account Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_days')
                ->label('Duration Days')
                ->searchable()
                ->sortable(),
            TextColumn::make('price')
                ->label('Price')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_all_subjects')
                ->label('Is All Subjects')
                ->boolean(),
            IconColumn::make('is_custom')
                ->label('Is Custom')
                ->boolean(),
            IconColumn::make('is_featured')
                ->label('Is Featured')
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
