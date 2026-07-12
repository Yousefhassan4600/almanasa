<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CitiesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('country_id')
                ->label('Country Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
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
