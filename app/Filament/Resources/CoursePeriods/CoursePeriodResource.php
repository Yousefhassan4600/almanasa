<?php

namespace App\Filament\Resources\CoursePeriods;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\CoursePeriods\Tables\CoursePeriodsTable;
use App\Models\CoursePeriod;
use Filament\Tables\Table;

class CoursePeriodResource extends BaseResource
{
    protected static ?string $model = CoursePeriod::class;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return CoursePeriodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoursePeriods::route('/'),
        ];
    }
}
