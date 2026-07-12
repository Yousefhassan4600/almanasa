<?php

namespace App\Filament\Resources\PaymentProofs;

use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentProofs\Pages\CreatePaymentProof;
use App\Filament\Resources\PaymentProofs\Pages\EditPaymentProof;
use App\Filament\Resources\PaymentProofs\Pages\ListPaymentProofs;
use App\Filament\Resources\PaymentProofs\Pages\ViewPaymentProof;
use App\Models\PaymentProof;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
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

class PaymentProofResource extends Resource
{
    protected static ?string $model = PaymentProof::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_id')
                    ->relationship('payment', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                    ->label('Payment')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('sender_phone')
                    ->tel(),
                TextInput::make('transfer_reference'),
                TextInput::make('receipt_path'),
                Select::make('status')
                    ->options(PaymentStatus::options())
                    ->required()
                    ->default(PaymentStatus::AwaitingReview->value),
                Select::make('reviewed_by')
                    ->relationship('reviewer', 'name')
                    ->label('Reviewed by')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('reviewed_at'),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('payment.display_name')
                    ->label('Payment'),
                TextEntry::make('sender_phone')
                    ->placeholder('-'),
                TextEntry::make('transfer_reference')
                    ->placeholder('-'),
                TextEntry::make('receipt_path')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('reviewer.name')
                    ->label('Reviewed by')
                    ->placeholder('-'),
                TextEntry::make('reviewed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('rejection_reason')
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
                TextColumn::make('payment.display_name')
                    ->label('Payment')
                    ->searchable(),
                TextColumn::make('sender_phone')
                    ->searchable(),
                TextColumn::make('transfer_reference')
                    ->searchable(),
                TextColumn::make('receipt_path')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('reviewer.name')
                    ->label('Reviewed by')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
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
            'index' => ListPaymentProofs::route('/'),
            'create' => CreatePaymentProof::route('/create'),
            'view' => ViewPaymentProof::route('/{record}'),
            'edit' => EditPaymentProof::route('/{record}/edit'),
        ];
    }
}
