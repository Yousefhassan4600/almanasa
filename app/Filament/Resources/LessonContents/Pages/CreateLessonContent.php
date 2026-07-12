<?php

namespace App\Filament\Resources\LessonContents\Pages;

use App\Filament\Resources\LessonContents\LessonContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLessonContent extends CreateRecord
{
    protected static string $resource = LessonContentResource::class;
}
