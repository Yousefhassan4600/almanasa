<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CitiesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#'),
            TextColumn::make('name')
                ->label('Name'),
            TextColumn::make('country.name')
                ->label('Country'),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
