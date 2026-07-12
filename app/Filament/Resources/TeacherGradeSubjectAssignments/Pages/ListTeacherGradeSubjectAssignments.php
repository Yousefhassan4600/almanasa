<?php

namespace App\Filament\Resources\TeacherGradeSubjectAssignments\Pages;

use App\Filament\Resources\TeacherGradeSubjectAssignments\TeacherGradeSubjectAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherGradeSubjectAssignments extends ListRecords
{
    protected static string $resource = TeacherGradeSubjectAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
