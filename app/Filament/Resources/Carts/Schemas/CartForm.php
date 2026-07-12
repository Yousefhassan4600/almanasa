<?php

namespace App\Filament\Resources\Carts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('provider_id')
                    ->label('Provider Id')
                    ->numeric()
                    ->required(),
                TextInput::make('status')
                    ->label('Status')
                    ->required(),
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->required(),
                TextInput::make('tax')
                    ->label('Tax')
                    ->numeric()
                    ->required(),
                TextInput::make('discount')
                    ->label('Discount')
                    ->numeric()
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->required(),
            ]);
    }
}
