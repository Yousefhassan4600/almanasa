<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Packages\PackageResource;

class ListPackages extends BaseListRecords
{
    protected static string $resource = PackageResource::class;
}
