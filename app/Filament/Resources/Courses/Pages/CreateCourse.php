<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Courses\CourseResource;

class CreateCourse extends BaseCreateRecord
{
    protected static string $resource = CourseResource::class;
}
