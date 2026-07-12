<?php

namespace App\Filament\Resources\AcademyTeacherGradeSubjects\Pages;

use App\Filament\Resources\AcademyTeacherGradeSubjects\AcademyTeacherGradeSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAcademyTeacherGradeSubjects extends ManageRecords
{
    protected static string $resource = AcademyTeacherGradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
