<?php

namespace App\Filament\Resources\GradeSubjects;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\GradeSubjects\Schemas\GradeSubjectForm;
use App\Filament\Resources\GradeSubjects\Tables\GradeSubjectsTable;
use App\Models\GradeSubject;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class GradeSubjectResource extends BaseResource
{
    protected static ?string $model = GradeSubject::class;

    protected static string|UnitEnum|null $navigationGroup = BaseResource::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?string $navigationParentItem = BaseResource::EDUCATION_CATALOG_NAVIGATION_PARENT;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return GradeSubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradeSubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeSubjects::route('/'),
            'create' => Pages\CreateGradeSubject::route('/create'),
            'edit' => Pages\EditGradeSubject::route('/{record}/edit'),
        ];
    }
}
