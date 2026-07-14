<?php

namespace App\Filament\Resources\PaymentMethods;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\PaymentMethods\Schemas\PaymentMethodForm;
use App\Filament\Resources\PaymentMethods\Tables\PaymentMethodsTable;
use App\Models\PaymentMethod;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class PaymentMethodResource extends BaseResource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|UnitEnum|null $navigationGroup = self::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PaymentMethodForm::configure($schema);
    }

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
