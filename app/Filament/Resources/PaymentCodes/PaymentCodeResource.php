<?php

namespace App\Filament\Resources\PaymentCodes;

use App\Filament\Resources\PaymentCodes\Pages\ManagePaymentCodes;
use App\Models\PaymentCode;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PaymentCodeResource extends Resource
{
    protected static ?string $model = PaymentCode::class;

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
                TextInput::make('code')
                    ->label('Code')
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric(),
                TextInput::make('duration_days')
                    ->label('Duration Days')
                    ->numeric(),
                TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->numeric()
                    ->required(),
                TextInput::make('used_count')
                    ->label('Used Count')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->label('Expires At'),
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
                TextColumn::make('amount')
                    ->label('Amount')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->label('Duration Days')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('max_uses')
                    ->label('Max Uses')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('used_count')
                    ->label('Used Count')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Is Active')
                    ->boolean(),
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
            'index' => ManagePaymentCodes::route('/'),
        ];
    }
}
