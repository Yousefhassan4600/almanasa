<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class CoursesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider.name')
                ->label('Provider')
                ->searchable()
                ->sortable(),
            TextColumn::make('accountSubject.name')
                ->label('Grade Subject')
                ->searchable(),
            TextColumn::make('academyTeacher.teacher.owner.name')
                ->label('Teacher')
                ->searchable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('num_of_lessons')
                ->label('Lessons')
                ->sortable(),
            TextColumn::make('num_of_hours')
                ->label('Hours')
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
