<?php

namespace App\Filament\Resources\OrderItems;

use App\Filament\Resources\OrderItems\Pages\CreateOrderItem;
use App\Filament\Resources\OrderItems\Pages\EditOrderItem;
use App\Filament\Resources\OrderItems\Pages\ListOrderItems;
use App\Filament\Resources\OrderItems\Pages\ViewOrderItem;
use App\Models\OrderItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Order')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('item_type')
                    ->required(),
                TextInput::make('item_id')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('metadata')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order.display_name')
                    ->label('Order'),
                TextEntry::make('item_type'),
                TextEntry::make('item_id')
                    ->numeric(),
                TextEntry::make('title'),
                TextEntry::make('unit_price')
                    ->money(),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('metadata')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
                TextColumn::make('order.display_name')
                    ->label('Order')
                    ->searchable(),
                TextColumn::make('item_type')
                    ->searchable(),
                TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
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
            'index' => ListOrderItems::route('/'),
            'create' => CreateOrderItem::route('/create'),
            'view' => ViewOrderItem::route('/{record}'),
            'edit' => EditOrderItem::route('/{record}/edit'),
        ];
    }
}
