<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class UsersTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('dial_country_code')
                ->label('Dial Country Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('phone')
                ->label('Phone')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Is Active')
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
