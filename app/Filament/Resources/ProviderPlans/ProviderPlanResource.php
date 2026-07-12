<?php

namespace App\Filament\Resources\ProviderPlans;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ProviderPlans\Pages\CreateProviderPlan;
use App\Filament\Resources\ProviderPlans\Pages\EditProviderPlan;
use App\Filament\Resources\ProviderPlans\Pages\ListProviderPlans;
use App\Filament\Resources\ProviderPlans\Schemas\ProviderPlanForm;
use App\Filament\Resources\ProviderPlans\Tables\ProviderPlansTable;
use App\Models\ProviderPlan;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ProviderPlanResource extends BaseResource
{
    protected static ?string $model = ProviderPlan::class;

    protected static string|UnitEnum|null $navigationGroup = 'Identity & Accounts';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ProviderPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProviderPlansTable::configure($table);
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
            'index' => ListProviderPlans::route('/'),
            'create' => CreateProviderPlan::route('/create'),
            'edit' => EditProviderPlan::route('/{record}/edit'),
        ];
    }
}
