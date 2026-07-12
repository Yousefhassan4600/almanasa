<?php

namespace App\Filament\Resources\PaymentCodes;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\PaymentCodes\Schemas\PaymentCodeForm;
use App\Filament\Resources\PaymentCodes\Tables\PaymentCodesTable;
use App\Models\PaymentCode;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PaymentCodeResource extends BaseResource
{
    protected static ?string $model = PaymentCode::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    public static function form(Schema $schema): Schema
    {
        return PaymentCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentCodes::route('/'),
            'create' => Pages\CreatePaymentCode::route('/create'),
            'edit' => Pages\EditPaymentCode::route('/{record}/edit'),
        ];
    }
}
