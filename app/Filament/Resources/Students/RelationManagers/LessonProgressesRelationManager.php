<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Base\RelationManagers\BaseRelationManager;
use App\Filament\Resources\LessonProgress\Tables\LessonProgressTable;
use Filament\Tables\Table;

class LessonProgressesRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'lessonProgresses';

    public function table(Table $table): Table
    {
        return LessonProgressTable::configure($table);
    }
}
