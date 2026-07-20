<?php

namespace App\Filament\Resources\Assignments\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class AssignmentsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->wrap(),
            TextColumn::make('course.title')
                ->label(__('admin.labels.Course'))
                ->wrap(),
            TextColumn::make('num_of_questions')
                ->label(__('admin.labels.Number of Questions'))
                ->badge()
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label(__('admin.labels.Duration'))
                ->badge()
                ->suffix(' min')
                ->sortable(),
            TextColumn::make('num_of_attempts')
                ->label(__('admin.labels.Number of Attempts'))
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
