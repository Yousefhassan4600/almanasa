<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\CourseReviews\Tables\CourseReviewsTable;
use Filament\Tables\Table;

class CourseReviewsRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'courseReviews';

    public function table(Table $table): Table
    {
        return CourseReviewsTable::configure($table);
    }
}
