<?php

namespace App\Filament\Resources\Exams\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class ExamsTable extends BaseTable
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
                ->searchable()
                ->wrap(),
            TextColumn::make('num_of_questions')
                ->label('Number of Questions')
                ->badge()
                ->sortable(),
            TextColumn::make('num_of_models')
                ->label('Number of Models')
                ->badge()
                ->sortable(),
            TextColumn::make('max_degree')
                ->label('Max Degree')
                ->badge()
                ->numeric()
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label('Duration')
                ->suffix(' min')
                ->badge()
                ->sortable(),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label('Ends At')
                ->dateTime()
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
