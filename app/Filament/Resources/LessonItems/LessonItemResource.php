<?php

namespace App\Filament\Resources\LessonItems;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\LessonItems\Schemas\LessonItemForm;
use App\Filament\Resources\LessonItems\Tables\LessonItemsTable;
use App\Models\LessonItem;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LessonItemResource extends BaseResource
{
    protected static ?string $model = LessonItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    public static function form(Schema $schema): Schema
    {
        return LessonItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessonItems::route('/'),
            'create' => Pages\CreateLessonItem::route('/create'),
            'edit' => Pages\EditLessonItem::route('/{record}/edit'),
        ];
    }
}
