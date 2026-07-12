<?php

namespace App\Filament\Resources\EducationStages;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\EducationStages\Schemas\EducationStageForm;
use App\Filament\Resources\EducationStages\Tables\EducationStagesTable;
use App\Models\EducationStage;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EducationStageResource extends BaseResource
{
    protected static ?string $model = EducationStage::class;

    protected static string|UnitEnum|null $navigationGroup = BaseResource::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?string $navigationParentItem = BaseResource::EDUCATION_CATALOG_NAVIGATION_PARENT;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EducationStageForm::configure($schema);
    }

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
            'create' => Pages\CreateEducationStage::route('/create'),
            'edit' => Pages\EditEducationStage::route('/{record}/edit'),
        ];
    }
}
