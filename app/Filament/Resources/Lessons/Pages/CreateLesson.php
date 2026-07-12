<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Lessons\LessonResource;

class CreateLesson extends BaseCreateRecord
{
    protected static string $resource = LessonResource::class;
}
