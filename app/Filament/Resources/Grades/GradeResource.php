<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\Grades\Schemas\GradeForm;
use App\Filament\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class GradeResource extends BaseResource
{
    protected static ?string $model = Grade::class;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 3;

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
        ];
    }
}
