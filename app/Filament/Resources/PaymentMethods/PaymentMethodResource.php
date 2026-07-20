<?php

namespace App\Filament\Resources\PaymentMethods;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\OrdersCatalog;
use App\Filament\Resources\PaymentMethods\Tables\PaymentMethodsTable;
use App\Models\PaymentMethod;
use Filament\Tables\Table;

class PaymentMethodResource extends BaseResource
{
    protected static ?string $model = PaymentMethod::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = OrdersCatalog::class;

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return PaymentMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
        ];
    }
}
