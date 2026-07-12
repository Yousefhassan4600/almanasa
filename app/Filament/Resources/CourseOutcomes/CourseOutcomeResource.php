<?php

namespace App\Filament\Resources\CourseOutcomes;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\CourseOutcomes\Schemas\CourseOutcomeForm;
use App\Filament\Resources\CourseOutcomes\Tables\CourseOutcomesTable;
use App\Models\CourseOutcome;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CourseOutcomeResource extends BaseResource
{
    protected static ?string $model = CourseOutcome::class;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return CourseOutcomeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseOutcomesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseOutcomes::route('/'),
            'create' => Pages\CreateCourseOutcome::route('/create'),
            'edit' => Pages\EditCourseOutcome::route('/{record}/edit'),
        ];
    }
}
