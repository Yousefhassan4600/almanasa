<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Support\CurrentAccount;

class CreateCourse extends BaseCreateRecord
{
    protected static string $resource = CourseResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! CurrentAccount::isSaasOwner() && CurrentAccount::providerId()) {
            $data['provider_id'] = CurrentAccount::providerId();
        }

        if (CurrentAccount::isAcademyTeacher()) {
            $data['academy_teacher_id'] = CurrentAccount::academyTeacherId();
        }

        if (CurrentAccount::isStandaloneTeacher()) {
            $data['academy_teacher_id'] = null;
        }

        return $data;
    }
}
