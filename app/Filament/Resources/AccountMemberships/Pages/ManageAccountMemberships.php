<?php

namespace App\Filament\Resources\AccountMemberships\Pages;

use App\Filament\Resources\AccountMemberships\AccountMembershipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountMemberships extends ManageRecords
{
    protected static string $resource = AccountMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
