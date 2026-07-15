<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class LessonsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course.title')
                ->label('Course')
                ->searchable()
                ->sortable(),
            TextColumn::make('coursePeriod.name')
                ->label('Period')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('sort_order')
                ->label('Sort')
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),
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
