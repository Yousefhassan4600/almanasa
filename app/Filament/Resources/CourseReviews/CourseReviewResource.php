<?php

namespace App\Filament\Resources\CourseReviews;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\CourseReviews\Schemas\CourseReviewForm;
use App\Filament\Resources\CourseReviews\Tables\CourseReviewsTable;
use App\Models\CourseReview;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CourseReviewResource extends BaseResource
{
    protected static ?string $model = CourseReview::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return CourseReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseReviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseReviews::route('/'),
            'create' => Pages\CreateCourseReview::route('/create'),
            'edit' => Pages\EditCourseReview::route('/{record}/edit'),
        ];
    }
}
