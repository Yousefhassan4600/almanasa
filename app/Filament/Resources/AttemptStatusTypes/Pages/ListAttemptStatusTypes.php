<?php

namespace App\Filament\Resources\AttemptStatusTypes\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\AttemptStatusTypes\AttemptStatusTypeResource;
use Override;

class ListAttemptStatusTypes extends BaseListRecords
{
    protected static string $resource = AttemptStatusTypeResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
