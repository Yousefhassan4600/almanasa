<?php

namespace App\Filament\Resources\PackageCourses;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\PackageCourses\Schemas\PackageCourseForm;
use App\Filament\Resources\PackageCourses\Tables\PackageCoursesTable;
use App\Models\PackageCourse;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PackageCourseResource extends BaseResource
{
    protected static ?string $model = PackageCourse::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return PackageCourseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageCoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackageCourses::route('/'),
            'create' => Pages\CreatePackageCourse::route('/create'),
            'edit' => Pages\EditPackageCourse::route('/{record}/edit'),
        ];
    }
}
