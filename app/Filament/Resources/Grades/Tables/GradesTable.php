<?php

namespace App\Filament\Resources\Grades\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class GradesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('education_stage_id')
                ->label('Education Stage Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
