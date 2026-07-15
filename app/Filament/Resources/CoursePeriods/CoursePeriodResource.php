<?php

namespace App\Filament\Resources\CoursePeriods;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\CoursePeriods\Schemas\CoursePeriodForm;
use App\Filament\Resources\CoursePeriods\Tables\CoursePeriodsTable;
use App\Models\CoursePeriod;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CoursePeriodResource extends BaseResource
{
    protected static ?string $model = CoursePeriod::class;

    protected static ?string $modelLabel = 'Course Period';

    protected static ?string $pluralModelLabel = 'Course Periods';

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CoursePeriodForm::configure($schema);
    }

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
            'create' => Pages\CreateCoursePeriod::route('/create'),
            'edit' => Pages\EditCoursePeriod::route('/{record}/edit'),
        ];
    }
}
