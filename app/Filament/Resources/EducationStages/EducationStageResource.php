<?php

namespace App\Filament\Resources\EducationStages;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\EducationCatalog;
use App\Filament\Resources\EducationStages\Tables\EducationStagesTable;
use App\Models\EducationStage;
use Filament\Tables\Table;

class EducationStageResource extends BaseResource
{
    protected static ?string $model = EducationStage::class;

    protected static ?string $cluster = EducationCatalog::class;

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return EducationStagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducationStages::route('/'),
        ];
    }
}
