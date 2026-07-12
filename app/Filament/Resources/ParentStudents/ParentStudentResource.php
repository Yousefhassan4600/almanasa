<?php

namespace App\Filament\Resources\ParentStudents;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ParentStudents\Schemas\ParentStudentForm;
use App\Filament\Resources\ParentStudents\Tables\ParentStudentsTable;
use App\Models\ParentStudent;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ParentStudentResource extends BaseResource
{
    protected static ?string $model = ParentStudent::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return ParentStudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentStudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentStudents::route('/'),
            'create' => Pages\CreateParentStudent::route('/create'),
            'edit' => Pages\EditParentStudent::route('/{record}/edit'),
        ];
    }
}
