<?php

namespace App\Filament\Resources\Orders;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cart_id')
                    ->label('Cart Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tax')
                    ->label('Tax')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount')
                    ->label('Discount')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
        ];
    }
}
