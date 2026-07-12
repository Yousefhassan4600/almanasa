<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Providers\ProviderResource;

class EditProvider extends BaseEditRecord
{
    protected static string $resource = ProviderResource::class;
}
