<?php

namespace App\Filament\Resources\AccountMemberships\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\AccountMemberships\AccountMembershipResource;

class ListAccountMemberships extends BaseListRecords
{
    protected static string $resource = AccountMembershipResource::class;
}
