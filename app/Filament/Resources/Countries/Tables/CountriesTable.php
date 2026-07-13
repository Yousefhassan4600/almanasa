<?php

namespace App\Filament\Resources\Countries\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CountriesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('code')
                ->label('Code')
                ->searchable()
                ->sortable(),
            TextColumn::make('phone_code')
                ->label('Phone Code')
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
