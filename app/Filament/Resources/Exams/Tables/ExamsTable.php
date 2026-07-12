<?php

namespace App\Filament\Resources\Exams\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ExamsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
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
            TextColumn::make('pass_score')
                ->label('Pass Score')
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
