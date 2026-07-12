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
            TextColumn::make('account_id')
                ->label('Account Id')
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
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label('Duration Minutes')
                ->searchable()
                ->sortable(),
            TextColumn::make('max_score')
                ->label('Max Score')
                ->searchable()
                ->sortable(),
            IconColumn::make('allow_retake')
                ->label('Allow Retake')
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
