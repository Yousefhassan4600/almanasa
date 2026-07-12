<?php

namespace App\Filament\Resources\CartItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CartItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cart_id')
                    ->label('Cart Id')
                    ->numeric()
                    ->required(),
                TextInput::make('course_id')
                    ->label('Course Id')
                    ->numeric(),
                TextInput::make('package_id')
                    ->label('Package Id')
                    ->numeric(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required(),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->required(),
            ]);
    }
}
