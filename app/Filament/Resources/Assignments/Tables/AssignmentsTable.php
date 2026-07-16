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
                ->searchable()
                ->wrap(),
            TextColumn::make('num_of_questions')
                ->label('Questions')
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label('Duration')
                ->suffix(' min')
                ->sortable(),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->dateTime()
                ->sortable(),
            IconColumn::make('is_today_only')
                ->label('Today Only')
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
