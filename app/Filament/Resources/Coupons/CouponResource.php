<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\ManageCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric(),
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                TextInput::make('discount_type')
                    ->label('Discount Type')
                    ->required(),
                TextInput::make('value')
                    ->label('Value')
                    ->numeric()
                    ->required(),
                TextInput::make('usage_limit')
                    ->label('Usage Limit')
                    ->numeric(),
                TextInput::make('usage_limit_per_user')
                    ->label('Usage Limit Per User')
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->label('Starts At'),
                DateTimePicker::make('ends_at')
                    ->label('Ends At'),
                Toggle::make('is_active')
                    ->label('Is Active'),
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
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->label('Discount Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->label('Usage Limit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('usage_limit_per_user')
                    ->label('Usage Limit Per User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Ends At')
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
            'index' => ManageCoupons::route('/'),
        ];
    }
}
