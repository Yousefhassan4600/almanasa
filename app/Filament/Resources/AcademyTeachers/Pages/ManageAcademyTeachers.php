<?php

namespace App\Filament\Resources\AcademyTeachers\Pages;

use App\Filament\Resources\AcademyTeachers\AcademyTeacherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAcademyTeachers extends ManageRecords
{
    protected static string $resource = AcademyTeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
