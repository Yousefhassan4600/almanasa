<?php

namespace App\Filament\Resources\StudentAnswers;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\StudentAnswers\Schemas\StudentAnswerForm;
use App\Filament\Resources\StudentAnswers\Tables\StudentAnswersTable;
use App\Models\StudentAnswer;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StudentAnswerResource extends BaseResource
{
    protected static ?string $model = StudentAnswer::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    public static function form(Schema $schema): Schema
    {
        return StudentAnswerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAnswersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAnswers::route('/'),
            'create' => Pages\CreateStudentAnswer::route('/create'),
            'edit' => Pages\EditStudentAnswer::route('/{record}/edit'),
        ];
    }
}
