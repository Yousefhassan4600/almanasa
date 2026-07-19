<?php

namespace App\Filament\Resources\Carts;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Carts\Tables\CartsTable;
use App\Models\Cart;
use Filament\Tables\Table;
use UnitEnum;

class CartResource extends BaseResource
{
    protected static ?string $model = Cart::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return CartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
        ];
    }
}
