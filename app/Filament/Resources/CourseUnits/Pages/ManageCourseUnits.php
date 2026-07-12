<?php

namespace App\Filament\Resources\CourseUnits\Pages;

use App\Filament\Resources\CourseUnits\CourseUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCourseUnits extends ManageRecords
{
    protected static string $resource = CourseUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
