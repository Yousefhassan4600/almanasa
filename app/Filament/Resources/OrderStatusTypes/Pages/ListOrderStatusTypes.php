<?php

namespace App\Filament\Resources\OrderStatusTypes\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\OrderStatusTypes\OrderStatusTypeResource;

class ListOrderStatusTypes extends BaseListRecords
{
    protected static string $resource = OrderStatusTypeResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
