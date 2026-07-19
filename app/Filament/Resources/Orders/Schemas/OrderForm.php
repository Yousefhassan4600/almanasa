<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\PurchaseType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('student_user_id')
                    ->label('Student')
                    ->relationship('student', 'phone')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('cart_id')
                    ->label('Cart')
                    ->relationship(
                        'cart',
                        'id',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->withoutTrashed(),
                    )
                    ->preload()
                    ->searchable(),
                TextInput::make('order_number')
                    ->label('Order Number')
                    ->required(),
                Select::make('purchase_type')
                    ->label('Purchase Type')
                    ->options(PurchaseType::options())
                    ->default(PurchaseType::SingleCourse->value)
                    ->required(),
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->required(),
            ])
            ->columns(2);
    }
}
