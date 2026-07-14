<?php

namespace App\Filament\Resources\Cities;

use App\Filament\Base\BaseResource;
use App\Filament\Clusters\Locations;
use App\Filament\Resources\Cities\Schemas\CityForm;
use App\Filament\Resources\Cities\Tables\CitiesTable;
use App\Models\City;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CityResource extends BaseResource
{
    protected static ?string $model = City::class;

    protected static ?string $cluster = Locations::class;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
        ];
    }
}
