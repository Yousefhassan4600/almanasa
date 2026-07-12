<?php

namespace App\Filament\Resources\StudentAttempts\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\StudentAttempts\StudentAttemptResource;

class ListStudentAttempts extends BaseListRecords
{
    protected static string $resource = StudentAttemptResource::class;
}
