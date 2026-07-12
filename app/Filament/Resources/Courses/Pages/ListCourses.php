<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Courses\CourseResource;

class ListCourses extends BaseListRecords
{
    protected static string $resource = CourseResource::class;
}
