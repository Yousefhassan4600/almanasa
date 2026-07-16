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
                ->label('#'),
            TextColumn::make('student.name')
                ->label('Student')
                ->searchable()
                ->sortable(),
            TextColumn::make('course.title')
                ->label('Course')
                ->searchable()
                ->sortable(),
            TextColumn::make('rating')
                ->label('Rating')
                ->suffix(' / 5')
                ->badge(),
            IconColumn::make('is_approved')
                ->label('Is Approved')
                ->boolean(),
            // ToggleColumn::make('is_approved')
            //     ->label('Is Approved'),
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
