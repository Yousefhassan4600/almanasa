<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;

class CreateStudentProfile extends BaseCreateRecord
{
    protected static string $resource = StudentProfileResource::class;
}
