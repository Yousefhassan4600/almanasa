<?php

namespace App\Filament\Resources\OrderItems\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\OrderItems\OrderItemResource;

class EditOrderItem extends BaseEditRecord
{
    protected static string $resource = OrderItemResource::class;
}
