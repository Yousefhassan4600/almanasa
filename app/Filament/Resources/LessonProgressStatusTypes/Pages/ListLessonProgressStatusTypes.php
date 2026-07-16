<?php

namespace App\Filament\Resources\LessonProgressStatusTypes\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\LessonProgressStatusTypes\LessonProgressStatusTypeResource;

class ListLessonProgressStatusTypes extends BaseListRecords
{
    protected static string $resource = LessonProgressStatusTypeResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
