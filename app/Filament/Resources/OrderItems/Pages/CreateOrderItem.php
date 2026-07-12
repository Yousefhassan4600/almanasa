<?php

namespace App\Filament\Resources\OrderItems\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\OrderItems\OrderItemResource;

class CreateOrderItem extends BaseCreateRecord
{
    protected static string $resource = OrderItemResource::class;
}
