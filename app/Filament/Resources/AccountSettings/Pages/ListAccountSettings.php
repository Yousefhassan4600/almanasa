<?php

namespace App\Filament\Resources\AccountSettings\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\AccountSettings\AccountSettingResource;

class ListAccountSettings extends BaseListRecords
{
    protected static string $resource = AccountSettingResource::class;
}
