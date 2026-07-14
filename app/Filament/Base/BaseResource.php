<?php

namespace App\Filament\Base;

use App\Concerns\FiltersByTenant;
use Filament\Resources\Resource;

abstract class BaseResource extends Resource
{
    use FiltersByTenant;

    public const PROJECT_DATA_NAVIGATION_GROUP = 'Project Data';

    protected static bool $isScopedToTenant = false;
}
