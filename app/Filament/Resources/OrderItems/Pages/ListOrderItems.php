<?php

namespace App\Filament\Resources\OrderItems\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\OrderItems\OrderItemResource;

class ListOrderItems extends BaseListRecords
{
    protected static string $resource = OrderItemResource::class;
}
