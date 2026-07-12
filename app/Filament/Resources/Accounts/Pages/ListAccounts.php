<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Accounts\AccountResource;

class ListAccounts extends BaseListRecords
{
    protected static string $resource = AccountResource::class;
}
