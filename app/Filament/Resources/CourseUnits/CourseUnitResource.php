<?php

namespace App\Filament\Resources\CourseUnits;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\CourseUnits\Schemas\CourseUnitForm;
use App\Filament\Resources\CourseUnits\Tables\CourseUnitsTable;
use App\Models\CourseUnit;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CourseUnitResource extends BaseResource
{
    protected static ?string $model = CourseUnit::class;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return CourseUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseUnits::route('/'),
            'create' => Pages\CreateCourseUnit::route('/create'),
            'edit' => Pages\EditCourseUnit::route('/{record}/edit'),
        ];
    }
}
