<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Lessons\LessonResource;

class EditLesson extends BaseEditRecord
{
    protected static string $resource = LessonResource::class;
}
