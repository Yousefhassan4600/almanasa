<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends BaseResource
{
    protected static ?string $model = Order::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
