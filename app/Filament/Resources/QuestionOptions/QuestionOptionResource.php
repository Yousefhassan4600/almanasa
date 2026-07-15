<?php

namespace App\Filament\Resources\QuestionOptions;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\QuestionOptions\Schemas\QuestionOptionForm;
use App\Filament\Resources\QuestionOptions\Tables\QuestionOptionsTable;
use App\Models\QuestionOption;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class QuestionOptionResource extends BaseResource
{
    protected static ?string $model = QuestionOption::class;

    // protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return QuestionOptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionOptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionOptions::route('/'),
            'create' => Pages\CreateQuestionOption::route('/create'),
            'edit' => Pages\EditQuestionOption::route('/{record}/edit'),
        ];
    }
}
