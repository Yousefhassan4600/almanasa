<?php

namespace App\Filament\Resources\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\Pages\ManagePayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_id')
                    ->label('Order Id')
                    ->numeric()
                    ->required(),
                TextInput::make('account_id')
                    ->label('Account Id')
                    ->numeric()
                    ->required(),
                TextInput::make('student_user_id')
                    ->label('Student User Id')
                    ->numeric()
                    ->required(),
                Select::make('method')
                    ->label('Method')
                    ->options(PaymentMethod::options())
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::options())
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),
                TextInput::make('transaction_reference')
                    ->label('Transaction Reference'),
                TextInput::make('payment_code')
                    ->label('Payment Code'),
                TextInput::make('sender_phone')
                    ->label('Sender Phone'),
                TextInput::make('transfer_image')
                    ->label('Transfer Image'),
                Textarea::make('gateway_response')
                    ->label('Gateway Response')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at')
                    ->label('Paid At'),
                TextInput::make('reviewed_by_user_id')
                    ->label('Reviewed By User Id')
                    ->numeric(),
                DateTimePicker::make('reviewed_at')
                    ->label('Reviewed At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_id')
                    ->label('Account Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_user_id')
                    ->label('Student User Id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Method')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transaction_reference')
                    ->label('Transaction Reference')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_code')
                    ->label('Payment Code')
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
            'index' => ManagePayments::route('/'),
        ];
    }
}
