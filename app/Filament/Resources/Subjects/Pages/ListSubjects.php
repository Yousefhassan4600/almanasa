<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Subject;
use Filament\Actions\CreateAction;

class ListSubjects extends BaseListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data): Subject {
                    $gradeIds = $data['grade_ids'] ?? [];

                    unset($data['grade_ids']);

                    $subject = Subject::query()->create($data);
                    $subject->syncGrades($gradeIds);

                    return $subject;
                }),
        ];
    }
}
