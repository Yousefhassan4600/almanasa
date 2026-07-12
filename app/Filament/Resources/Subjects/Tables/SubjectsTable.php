<?php

namespace App\Filament\Resources\Subjects\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class SubjectsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('icon')
                ->label('Icon')
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
