<?php

namespace App\Filament\Resources\Countries;

use App\Filament\Base\BaseResource;
use App\Filament\Resources\Countries\Schemas\CountryForm;
use App\Filament\Resources\Countries\Tables\CountriesTable;
use App\Models\Country;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CountryResource extends BaseResource
{
    protected static ?string $model = Country::class;

    protected static string|UnitEnum|null $navigationGroup = BaseResource::PROJECT_DATA_NAVIGATION_GROUP;

    protected static ?string $navigationParentItem = BaseResource::LOCATIONS_NAVIGATION_PARENT;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CountryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CountriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
