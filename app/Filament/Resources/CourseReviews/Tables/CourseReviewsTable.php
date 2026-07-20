<?php

namespace App\Filament\Resources\CourseReviews\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class CourseReviewsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('admin.labels.#')),
            TextColumn::make('student.name')
                ->label(__('admin.labels.Student'))
                ->searchable()
                ->sortable(),
            TextColumn::make('course.title')
                ->label(__('admin.labels.Course'))
                ->searchable()
                ->sortable(),
            TextColumn::make('rating')
                ->label(__('admin.labels.Rating'))
                ->suffix(' / 5')
                ->badge(),
            IconColumn::make('is_approved')
                ->label(__('admin.labels.Is Approved'))
                ->boolean(),
            // ToggleColumn::make('is_approved')
            //     ->label(__('admin.labels.Is Approved')),
        ];
    }

    public function hasViewAction(): bool
    {
        return false;
    }

    public function hasEditAction(): bool
    {
        return false;
    }
}
