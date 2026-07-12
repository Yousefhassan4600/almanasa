<?php

namespace App\Filament\Resources\StudentAcademicProfiles\Pages;

use App\Filament\Resources\StudentAcademicProfiles\StudentAcademicProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentAcademicProfiles extends ListRecords
{
    protected static string $resource = StudentAcademicProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
