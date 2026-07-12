<?php

namespace App\Filament\Resources\AcademyTeacherGradeSubjects;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AcademyTeacherGradeSubjects\Schemas\AcademyTeacherGradeSubjectForm;
use App\Filament\Resources\AcademyTeacherGradeSubjects\Tables\AcademyTeacherGradeSubjectsTable;
use App\Models\AcademyTeacherGradeSubject;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AcademyTeacherGradeSubjectResource extends BaseResource
{
    protected static ?string $model = AcademyTeacherGradeSubject::class;

    protected static string|UnitEnum|null $navigationGroup = 'Education Setup';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return AcademyTeacherGradeSubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademyTeacherGradeSubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademyTeacherGradeSubjects::route('/'),
            'create' => Pages\CreateAcademyTeacherGradeSubject::route('/create'),
            'edit' => Pages\EditAcademyTeacherGradeSubject::route('/{record}/edit'),
        ];
    }
}
