<?php

namespace App\Filament\Resources\CourseOutcomes\Pages;

use App\Filament\Resources\CourseOutcomes\CourseOutcomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCourseOutcomes extends ManageRecords
{
    protected static string $resource = CourseOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
