<?php

namespace App\Filament\Resources\Assignments\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AssignmentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->wrap(),
            TextColumn::make('course.title')
                ->label('Course')
                ->wrap(),
            TextColumn::make('num_of_questions')
                ->label('Number of Questions')
                ->badge()
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label('Duration')
                ->badge()
                ->suffix(' min')
                ->sortable(),
            TextColumn::make('num_of_attempts')
                ->label('Number of Attempts')
                ->badge()
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
