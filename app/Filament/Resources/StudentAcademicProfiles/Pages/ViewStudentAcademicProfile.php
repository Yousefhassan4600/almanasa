<?php

namespace App\Filament\Resources\StudentAcademicProfiles\Pages;

use App\Filament\Resources\StudentAcademicProfiles\StudentAcademicProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentAcademicProfile extends ViewRecord
{
    protected static string $resource = StudentAcademicProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
