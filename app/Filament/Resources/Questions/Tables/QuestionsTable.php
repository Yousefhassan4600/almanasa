<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class QuestionsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'lesson.course',
        ];
    }

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
            TextColumn::make('lesson.course.title')
                ->label(__('admin.labels.Course'))
                ->searchable()
                ->wrap(),
            TextColumn::make('lesson.title')
                ->label(__('admin.labels.Lesson'))
                ->searchable()
                ->wrap(),
            TextColumn::make('type')
                ->label(__('admin.labels.Type'))
                ->badge()
                ->sortable(),
            TextColumn::make('difficulty')
                ->label(__('admin.labels.Difficulty'))
                ->badge()
                ->sortable(),
        ];
    }

    protected function getDefaultSort(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultOrder(): ?string
    {
        return 'asc';
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
