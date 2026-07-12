<?php

namespace App\Filament\Resources\CourseSections\Pages;

use App\Filament\Resources\CourseSections\CourseSectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseSection extends ViewRecord
{
    protected static string $resource = CourseSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
