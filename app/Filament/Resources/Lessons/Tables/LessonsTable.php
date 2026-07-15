<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label('#')
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label('Title')
                ->searchable(),
            TextColumn::make('course.title')
                ->label('Course'),
            TextColumn::make('course.provider.name')
                ->label('Provider'),
            TextColumn::make('course.academyTeacher.teacher.owner.name')
                ->label('Teacher'),
            TextColumn::make('coursePeriod.name')
                ->label('Period'),
            TextColumn::make('starts_at')
                ->label('Starts At')
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label('Ends At')
                ->dateTime()
                ->sortable(),
            TextColumn::make('num_of_video_views')
                ->label('Video Views')
                ->numeric()
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label('Active'),
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
