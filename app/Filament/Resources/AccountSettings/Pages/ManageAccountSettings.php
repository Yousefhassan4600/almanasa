<?php

namespace App\Filament\Resources\AccountSettings\Pages;

use App\Filament\Resources\AccountSettings\AccountSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountSettings extends ManageRecords
{
    protected static string $resource = AccountSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
