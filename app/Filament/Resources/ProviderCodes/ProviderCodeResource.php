<?php

namespace App\Filament\Resources\ProviderCodes;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\ProviderCodes\Schemas\ProviderCodeForm;
use App\Filament\Resources\ProviderCodes\Tables\ProviderCodesTable;
use App\Models\ProviderCode;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ProviderCodeResource extends BaseResource
{
    protected static ?string $model = ProviderCode::class;

    protected static string|UnitEnum|null $navigationGroup = 'Sales & Payments';

    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return 'Codes';
    }

    public static function getPluralLabel(): string
    {
        return 'Codes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Codes';
    }

    public static function getModelLabel(): string
    {
        return 'Code';
    }

    public static function form(Schema $schema): Schema
    {
        return ProviderCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProviderCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderCodes::route('/'),
        ];
    }
}
