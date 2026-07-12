<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Users\UserResource;

class ListUsers extends BaseListRecords
{
    protected static string $resource = UserResource::class;
}
