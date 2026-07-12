<?php

namespace App\Filament\Resources\StudentEnrollments\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\StudentEnrollments\StudentEnrollmentResource;

class ListStudentEnrollments extends BaseListRecords
{
    protected static string $resource = StudentEnrollmentResource::class;
}
