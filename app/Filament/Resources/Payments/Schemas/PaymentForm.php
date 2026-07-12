<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_id')
                    ->label('Order Id')
                    ->numeric()
                    ->required(),
                TextInput::make('provider_id')
                    ->label('Provider Id')
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
}
