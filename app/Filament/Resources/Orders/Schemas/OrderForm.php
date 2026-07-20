<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\PurchaseType;
use App\Filament\Support\CurrentAccount;
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
                CurrentAccount::providerSelect(Select::make('provider_id'))
                    ->label(__('admin.labels.Provider'))
                    ->relationship('provider', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('student_user_id')
                    ->label(__('admin.labels.Student'))
                    ->relationship('student', 'phone')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('cart_id')
                    ->label(__('admin.labels.Cart'))
                    ->relationship(
                        'cart',
                        'id',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->withoutTrashed(),
                    )
                    ->preload()
                    ->searchable(),
                TextInput::make('order_number')
                    ->label(__('admin.labels.Order Number'))
                    ->required(),
                Select::make('purchase_type')
                    ->label(__('admin.labels.Purchase Type'))
                    ->options(PurchaseType::options())
                    ->default(PurchaseType::SingleCourse->value)
                    ->required(),
                TextInput::make('subtotal')
                    ->label(__('admin.labels.Subtotal'))
                    ->numeric()
                    ->required(),
                TextInput::make('total')
                    ->label(__('admin.labels.Total'))
                    ->numeric()
                    ->required(),
            ])
            ->columns(2);
    }
}
