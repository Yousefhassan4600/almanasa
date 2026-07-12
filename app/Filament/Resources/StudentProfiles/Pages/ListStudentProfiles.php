<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;

class ListStudentProfiles extends BaseListRecords
{
    protected static string $resource = StudentProfileResource::class;
}
