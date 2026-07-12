<?php

namespace App\Filament\Resources\AccountMemberships\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\AccountMemberships\AccountMembershipResource;

class CreateAccountMembership extends BaseCreateRecord
{
    protected static string $resource = AccountMembershipResource::class;
}
