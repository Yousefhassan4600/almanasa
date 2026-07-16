<?php

namespace App\Filament\Resources\LessonProgress;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\LessonProgress\Tables\LessonProgressTable;
use App\Models\LessonProgress;
use Filament\Tables\Table;
use UnitEnum;

class LessonProgressResource extends BaseResource
{
    protected static ?string $model = LessonProgress::class;

    protected static string|UnitEnum|null $navigationGroup = 'Students & Families';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return LessonProgressTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessonProgress::route('/'),
        ];
    }
}
