<?php

namespace App\Filament\Resources\PurchaseUnits;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\PurchaseUnits\Schemas\PurchaseUnitForm;
use App\Filament\Resources\PurchaseUnits\Tables\PurchaseUnitsTable;
use App\Models\PurchaseUnit;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PurchaseUnitResource extends BaseResource
{
    protected static ?string $model = PurchaseUnit::class;

    protected static ?string $modelLabel = 'Purchase Unit';

    protected static ?string $pluralModelLabel = 'Purchase Units';

    protected static string|UnitEnum|null $navigationGroup = 'Learning Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PurchaseUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseUnits::route('/'),
            'create' => Pages\CreatePurchaseUnit::route('/create'),
            'edit' => Pages\EditPurchaseUnit::route('/{record}/edit'),
        ];
    }
}
