<?php

namespace App\Filament\Resources\TeacherGradeSubjectAssignments\Pages;

use App\Filament\Resources\TeacherGradeSubjectAssignments\TeacherGradeSubjectAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherGradeSubjectAssignment extends EditRecord
{
    protected static string $resource = TeacherGradeSubjectAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
