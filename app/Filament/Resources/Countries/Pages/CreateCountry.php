<?php

namespace App\Filament\Resources\Countries\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Countries\CountryResource;

class CreateCountry extends BaseCreateRecord
{
    protected static string $resource = CountryResource::class;
}
