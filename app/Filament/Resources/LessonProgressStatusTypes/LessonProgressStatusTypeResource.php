<?php

namespace App\Filament\Resources\LessonProgressStatusTypes;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\LessonProgressStatusTypes\Tables\LessonProgressStatusTypesTable;
use App\Models\LessonProgressStatusType;
use Filament\Tables\Table;

class LessonProgressStatusTypeResource extends BaseResource
{
    protected static ?string $model = LessonProgressStatusType::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 8;

    public static function table(Table $table): Table
    {
        return LessonProgressStatusTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessonProgressStatusTypes::route('/'),
        ];
    }
}
