<?php

namespace App\Filament\Resources\EducationStages\Pages;

use App\Filament\Resources\EducationStages\EducationStageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEducationStages extends ManageRecords
{
    protected static string $resource = EducationStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
