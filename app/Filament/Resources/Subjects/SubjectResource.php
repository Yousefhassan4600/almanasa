<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\Subjects\Schemas\SubjectForm;
use App\Filament\Resources\Subjects\Tables\SubjectsTable;
use App\Models\Subject;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubjectResource extends BaseResource
{
    protected static ?string $model = Subject::class;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return SubjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
        ];
    }
}
