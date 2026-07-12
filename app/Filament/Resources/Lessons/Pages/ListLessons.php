<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Lessons\LessonResource;

class ListLessons extends BaseListRecords
{
    protected static string $resource = LessonResource::class;
}
