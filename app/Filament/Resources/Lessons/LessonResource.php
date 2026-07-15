<?php

namespace App\Filament\Resources\Lessons;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Lessons\RelationManagers\LessonItemsRelationManager;
use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Lessons\Tables\LessonsTable;
use App\Models\Lesson;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LessonResource extends BaseResource
{
    protected static ?string $model = Lesson::class;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LessonItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
