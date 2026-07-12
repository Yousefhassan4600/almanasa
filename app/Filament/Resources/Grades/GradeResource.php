<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Grades\Schemas\GradeForm;
use App\Filament\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class GradeResource extends BaseResource
{
    protected static ?string $model = Grade::class;

    protected static string|UnitEnum|null $navigationGroup = BaseResource::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?string $navigationParentItem = BaseResource::EDUCATION_CATALOG_NAVIGATION_PARENT;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
