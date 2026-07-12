<?php

namespace App\Filament\Base;

use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    public const PROJECT_DATA_NAVIGATION_GROUP = 'Project Data';

    public const EDUCATION_CATALOG_NAVIGATION_PARENT = 'Education Catalog';

    public const LOCATIONS_NAVIGATION_PARENT = 'Locations';

    protected static bool $isScopedToTenant = false;
}
