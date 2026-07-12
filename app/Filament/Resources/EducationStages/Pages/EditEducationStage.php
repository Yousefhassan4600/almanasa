<?php

namespace App\Filament\Resources\EducationStages\Pages;

use App\Filament\Resources\EducationStages\EducationStageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEducationStage extends EditRecord
{
    protected static string $resource = EducationStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
