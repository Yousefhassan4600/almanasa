<?php

namespace App\Filament\Resources\Assignments;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Assignments\Schemas\AssignmentForm;
use App\Filament\Resources\Assignments\Tables\AssignmentsTable;
use App\Models\Assignment;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class AssignmentResource extends BaseResource
{
    protected static ?string $model = Assignment::class;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return AssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
