<?php

namespace App\Filament\Resources\OrderStatusTypes;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\OrdersCatalog;
use App\Filament\Resources\OrderStatusTypes\Tables\OrderStatusTypesTable;
use App\Models\OrderStatusType;
use Filament\Tables\Table;

class OrderStatusTypeResource extends BaseResource
{
    protected static ?string $model = OrderStatusType::class;

    protected static ?string $cluster = OrdersCatalog::class;

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return OrderStatusTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderStatusTypes::route('/'),
        ];
    }
}
