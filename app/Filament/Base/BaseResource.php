<?php

namespace App\Filament\Base;

use App\Concerns\FiltersByTenant;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    use FiltersByTenant;

    public const PROJECT_DATA_NAVIGATION_GROUP = 'Project Data';

    public const EDUCATION_CATALOG_NAVIGATION_PARENT = 'Education Catalog';

    public const LOCATIONS_NAVIGATION_PARENT = 'Locations';

    protected static bool $isScopedToTenant = false;
}
