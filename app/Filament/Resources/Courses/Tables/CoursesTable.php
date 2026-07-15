<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CoursesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('academyTeacher.teacher.owner.name')
                ->label('Teacher')
                ->searchable(),
            TextColumn::make('accountSubject.name')
                ->label('Grade Subject')
                ->searchable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('weekly_lectures_count')
                ->label('Weekly Lessons')
                ->badge()
                ->sortable(),
            TextColumn::make('num_of_lessons')
                ->label('Lessons')
                ->badge()
                ->sortable(),
            TextColumn::make('num_of_hours')
                ->label('Hours')
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
