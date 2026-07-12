<?php

namespace App\Filament\Resources\GradeSubjects\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class GradeSubjectsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('grade.name')
                ->label('Grade')
                ->searchable()
                ->sortable(),
            TextColumn::make('subject.name')
                ->label('Subject')
                ->searchable()
                ->sortable(),
            TextColumn::make('track.name')
                ->label('Track')
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
