<?php

namespace App\Filament\Resources\Lessons\RelationManagers\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonItemsTable extends BaseTable
{
    protected function eagerLoads(): array
    {
        return [
            'assignment',
            'exam',
        ];
    }

    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->searchable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->wrap(),
            TextColumn::make('type')
                ->label(__('admin.labels.Type'))
                ->badge()
                ->wrap(),
            TextColumn::make('assignment.title')
                ->label(__('admin.labels.Assignment'))
                ->badge(),
            TextColumn::make('exam.title')
                ->label(__('admin.labels.Exam'))
                ->badge(),
            TextColumn::make('duration_minutes')
                ->label(__('admin.labels.Duration Minutes'))
                ->badge()
                ->sortable(),
            TextColumn::make('starts_at')
                ->label(__('admin.labels.Starts At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label(__('admin.labels.Ends At'))
                ->dateTime()
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label(__('admin.labels.Active')),
            ToggleColumn::make('is_free')
                ->label(__('admin.labels.Free')),
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
}
