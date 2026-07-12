<?php

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Countries\CountryResource;

class ListCountries extends BaseListRecords
{
    protected static string $resource = CountryResource::class;
}
