<?php

namespace App\Filament\Resources\EducationStages\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\EducationStages\EducationStageResource;

class ListEducationStages extends BaseListRecords
{
    protected static string $resource = EducationStageResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
