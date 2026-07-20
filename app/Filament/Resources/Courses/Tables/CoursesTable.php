<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Base\BaseTable;
use App\Filament\Support\CurrentAccount;
use Filament\Tables\Columns\TextColumn;

class CoursesTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#'))
                ->sortable(),
            TextColumn::make('provider.name')
                ->label(__('admin.labels.Provider'))
                ->visible(fn (): bool => CurrentAccount::isSaasOwner())
                // ->searchable()
                ->sortable(),
            TextColumn::make('academyTeacher.teacher.owner.name')
                ->label(__('admin.labels.Teacher'))
                ->visible(fn (): bool => ! CurrentAccount::isAcademyTeacher() && ! CurrentAccount::isStandaloneTeacher()),
            TextColumn::make('accountSubject.name')
                ->label(__('admin.labels.Grade Subject')),
            // ->searchable(),
            TextColumn::make('title')
                ->label(__('admin.labels.Title'))
                ->searchable()
                ->sortable(),
            TextColumn::make('weekly_lectures_count')
                ->label(__('admin.labels.Weekly Lessons'))
                ->badge()
                ->sortable(),
            TextColumn::make('num_of_lessons')
                ->label(__('admin.labels.Lessons'))
                ->badge()
                ->sortable(),
            TextColumn::make('num_of_hours')
                ->label(__('admin.labels.Hours'))
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
