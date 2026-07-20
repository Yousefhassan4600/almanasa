<?php

namespace App\Filament\Resources\PurchaseUnits;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\OrdersCatalog;
use App\Filament\Resources\PurchaseUnits\Tables\PurchaseUnitsTable;
use App\Models\PurchaseUnit;
use Filament\Tables\Table;

class PurchaseUnitResource extends BaseResource
{
    protected static ?string $model = PurchaseUnit::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = OrdersCatalog::class;

    protected static ?int $navigationSort = 1;

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
        ];
    }
}
