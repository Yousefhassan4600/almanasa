<?php

namespace App\Filament\Resources\CartItems;

use App\Filament\Resources\CartItems\Pages\CreateCartItem;
use App\Filament\Resources\CartItems\Pages\EditCartItem;
use App\Filament\Resources\CartItems\Pages\ListCartItems;
use App\Filament\Resources\CartItems\Pages\ViewCartItem;
use App\Models\CartItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cart_id')
                    ->relationship('cart', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Cart')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('item_type')
                    ->required(),
                TextInput::make('item_id')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cart.display_name')
                    ->label('Cart'),
                TextEntry::make('item_type'),
                TextEntry::make('item_id')
                    ->numeric(),
                TextEntry::make('unit_price')
                    ->money(),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cart.display_name')
                    ->label('Cart')
                    ->searchable(),
                TextColumn::make('item_type')
                    ->searchable(),
                TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCartItems::route('/'),
            'create' => CreateCartItem::route('/create'),
            'view' => ViewCartItem::route('/{record}'),
            'edit' => EditCartItem::route('/{record}/edit'),
        ];
    }
}
