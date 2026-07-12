<?php

namespace App\Filament\Resources\GradeSubjects\Pages;

use App\Filament\Resources\GradeSubjects\GradeSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGradeSubjects extends ManageRecords
{
    protected static string $resource = GradeSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
