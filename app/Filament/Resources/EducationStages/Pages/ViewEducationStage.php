<?php

namespace App\Filament\Resources\EducationStages\Pages;

use App\Filament\Resources\EducationStages\EducationStageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEducationStage extends ViewRecord
{
    protected static string $resource = EducationStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
