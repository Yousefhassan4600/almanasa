<?php

namespace App\Filament\Resources\CourseReviews\Tables;

use App\Filament\Base\BaseTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class CourseReviewsTable extends BaseTable
{
    protected function columns(): array
    {
        return [
            TextColumn::make('student_user_id')
                ->label('Student User Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('provider_id')
                ->label('Provider Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('course_id')
                ->label('Course Id')
                ->searchable()
                ->sortable(),
            TextColumn::make('rating')
                ->label('Rating')
                ->searchable()
                ->sortable(),
            IconColumn::make('is_approved')
                ->label('Is Approved')
                ->boolean(),
        ];
    }

    protected function extraFilters(): array
    {
        return [
            //
        ];
    }
}
