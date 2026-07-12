<?php

namespace App\Filament\Resources\TeacherGradeSubjectAssignments\Pages;

use App\Filament\Resources\TeacherGradeSubjectAssignments\TeacherGradeSubjectAssignmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacherGradeSubjectAssignment extends ViewRecord
{
    protected static string $resource = TeacherGradeSubjectAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
