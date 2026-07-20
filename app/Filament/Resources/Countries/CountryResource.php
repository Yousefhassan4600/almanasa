<?php

namespace App\Filament\Resources\Countries;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\Locations;
use App\Filament\Resources\Countries\Schemas\CountryForm;
use App\Filament\Resources\Countries\Tables\CountriesTable;
use App\Models\Country;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CountryResource extends BaseResource
{
    protected static ?string $model = Country::class;

    protected static bool $isSaasOwnerOnly = true;

    protected static ?string $cluster = Locations::class;

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
        ];
    }
}
