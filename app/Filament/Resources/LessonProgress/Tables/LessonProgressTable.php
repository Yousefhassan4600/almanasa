<?php

namespace App\Filament\Resources\LessonProgress\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class LessonProgressTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('lesson_id')
                ->label('Lesson Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            TextColumn::make('watched_seconds')
                ->label('Watched Seconds')
                ->searchable()
                ->sortable(),
            TextColumn::make('required_seconds')
                ->label('Required Seconds')
                ->searchable()
                ->sortable(),
            TextColumn::make('completion_percentage')
                ->label('Completion Percentage')
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
