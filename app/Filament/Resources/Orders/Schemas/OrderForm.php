<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                TextInput::make('cart_id')
                    ->label('Cart Id')
                    ->numeric(),
                TextInput::make('order_number')
                    ->label('Order Number')
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
                Select::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::options())
                    ->required(),
            ]);
    }
}
