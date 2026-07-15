<?php

namespace App\Filament\Resources\LessonItems\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
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
            TextColumn::make('lesson.title')
                ->label('Lesson')
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
            TextColumn::make('link_url')
                ->label('Link Url')
                ->searchable()
                ->sortable(),
            TextColumn::make('duration_minutes')
                ->label('Duration Minutes')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),
            IconColumn::make('is_free')
                ->label('Free')
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
