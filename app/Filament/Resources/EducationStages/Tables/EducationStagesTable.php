<?php

namespace App\Filament\Resources\EducationStages\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class EducationStagesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
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
