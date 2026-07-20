<?php

namespace App\Filament\Resources\ProviderSubscriptions;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ProviderSubscriptions\Pages\CreateProviderSubscription;
use App\Filament\Resources\ProviderSubscriptions\Pages\EditProviderSubscription;
use App\Filament\Resources\ProviderSubscriptions\Pages\ListProviderSubscriptions;
use App\Filament\Resources\ProviderSubscriptions\Schemas\ProviderSubscriptionForm;
use App\Filament\Resources\ProviderSubscriptions\Tables\ProviderSubscriptionsTable;
use App\Models\ProviderSubscription;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ProviderSubscriptionResource extends BaseResource
{
    protected static ?string $model = ProviderSubscription::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Subscriptions';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ProviderSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProviderSubscriptionsTable::configure($table);
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
            'index' => ListProviderSubscriptions::route('/'),
            'create' => CreateProviderSubscription::route('/create'),
            'edit' => EditProviderSubscription::route('/{record}/edit'),
        ];
    }
}
