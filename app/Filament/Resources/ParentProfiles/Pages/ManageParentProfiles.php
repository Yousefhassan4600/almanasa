<?php

namespace App\Filament\Resources\ParentProfiles\Pages;

use App\Filament\Resources\ParentProfiles\ParentProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageParentProfiles extends ManageRecords
{
    protected static string $resource = ParentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
