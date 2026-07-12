<?php

namespace App\Filament\Resources\LessonItems\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;

class LessonItemsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('lesson_id')
                ->label('Lesson Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Type')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->sortable(),
            TextColumn::make('video_url')
                ->label('Video Url')
                ->searchable()
                ->sortable(),
            TextColumn::make('file_url')
                ->label('File Url')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_seconds')
                ->label('Duration Seconds')
                ->searchable()
                ->sortable(),
            TextColumn::make('assignment_id')
                ->label('Assignment Id')
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
