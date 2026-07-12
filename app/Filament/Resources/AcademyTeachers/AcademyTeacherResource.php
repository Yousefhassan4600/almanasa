<?php

namespace App\Filament\Resources\AcademyTeachers;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\AcademyTeachers\Schemas\AcademyTeacherForm;
use App\Filament\Resources\AcademyTeachers\Tables\AcademyTeachersTable;
use App\Models\AcademyTeacher;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AcademyTeacherResource extends BaseResource
{
    protected static ?string $model = AcademyTeacher::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return AcademyTeacherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademyTeachersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademyTeachers::route('/'),
            'create' => Pages\CreateAcademyTeacher::route('/create'),
            'edit' => Pages\EditAcademyTeacher::route('/{record}/edit'),
        ];
    }
}
