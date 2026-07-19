<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Students\StudentResource;

class ListStudents extends BaseListRecords
{
    protected static string $resource = StudentResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
