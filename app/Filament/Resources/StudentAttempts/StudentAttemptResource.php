<?php

namespace App\Filament\Resources\StudentAttempts;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\StudentAttempts\Schemas\StudentAttemptForm;
use App\Filament\Resources\StudentAttempts\Tables\StudentAttemptsTable;
use App\Models\StudentAttempt;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StudentAttemptResource extends BaseResource
{
    protected static ?string $model = StudentAttempt::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return StudentAttemptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAttempts::route('/'),
            'create' => Pages\CreateStudentAttempt::route('/create'),
            'edit' => Pages\EditStudentAttempt::route('/{record}/edit'),
        ];
    }
}
