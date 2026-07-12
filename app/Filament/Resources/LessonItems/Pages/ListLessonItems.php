<?php

namespace App\Filament\Resources\LessonItems\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\LessonItems\LessonItemResource;

class ListLessonItems extends BaseListRecords
{
    protected static string $resource = LessonItemResource::class;
}
