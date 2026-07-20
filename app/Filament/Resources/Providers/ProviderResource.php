<?php

namespace App\Filament\Resources\Providers;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Providers\Pages\CreateProvider;
use App\Filament\Resources\Providers\Pages\EditProvider;
use App\Filament\Resources\Providers\Pages\ListProviders;
use App\Filament\Resources\Providers\RelationManagers\ProviderPaymentMethodsRelationManager;
use App\Filament\Resources\Providers\RelationManagers\SlidersRelationManager;
use App\Filament\Resources\Providers\Schemas\ProviderForm;
use App\Filament\Resources\Providers\Tables\ProvidersTable;
use App\Models\Provider;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ProviderResource extends BaseResource
{
    protected static ?string $model = Provider::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static string|UnitEnum|null $navigationGroup = 'Users & Subscriptions';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvidersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProviderPaymentMethodsRelationManager::class,
            SlidersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviders::route('/'),
            'create' => CreateProvider::route('/create'),
            'edit' => EditProvider::route('/{record}/edit'),
        ];
    }
}
