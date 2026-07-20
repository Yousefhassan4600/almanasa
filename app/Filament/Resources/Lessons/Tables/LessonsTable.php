<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class LessonsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->searchable()
                ->sortable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable(),
            TextColumn::make('course.title')
                ->label(__('admin.labels.Course')),
            TextColumn::make('course.provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner()),
            TextColumn::make('course.academyTeacher.teacher.owner.name')
                ->label(__('admin.labels.Teacher'))
                ->visible(fn (): bool => ! CurrentAccount::isAcademyTeacher()),
            TextColumn::make('coursePeriod.name')
                ->label(__('admin.labels.Period')),
            TextColumn::make('starts_at')
                ->label(__('admin.labels.Starts At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('ends_at')
                ->label(__('admin.labels.Ends At'))
                ->dateTime()
                ->sortable(),
            TextColumn::make('num_of_video_views')
                ->label(__('admin.labels.Video Views'))
                ->numeric()
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label(__('admin.labels.Active')),
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
