<?php

namespace App\Filament\Resources\Countries\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CountriesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#'),
            TextColumn::make('name')
                ->label('Name'),
            TextColumn::make('code')
                ->label('Code'),
            TextColumn::make('phone_code')
                ->label('Phone Code'),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
