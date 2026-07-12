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
            TextColumn::make('course_unit_id')
                ->label('Course Unit Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_seconds')
                ->label('Duration Seconds')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_free')
                ->label('Is Free')
                ->boolean(),
            TextColumn::make('status')
                ->label('Status')
                ->searchable()
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
