<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Orders\OrderResource;

class CreateOrder extends BaseCreateRecord
{
    protected static string $resource = OrderResource::class;
}
