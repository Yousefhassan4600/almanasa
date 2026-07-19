<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Orders\OrderResource;

class ListOrders extends BaseListRecords
{
    protected static string $resource = OrderResource::class;

    protected function hasCreateAction(): bool
    {
        return false;
    }
}
