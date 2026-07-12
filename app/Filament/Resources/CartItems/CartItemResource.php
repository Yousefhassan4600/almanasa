<?php

namespace App\Filament\Resources\CartItems;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\CartItems\Schemas\CartItemForm;
use App\Filament\Resources\CartItems\Tables\CartItemsTable;
use App\Models\CartItem;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CartItemResource extends BaseResource
{
    protected static ?string $model = CartItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return CartItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CartItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }
}
