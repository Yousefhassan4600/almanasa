<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Base\Pages\BaseEditRecord;
use App\Filament\Resources\Orders\OrderResource;

class EditOrder extends BaseEditRecord
{
    protected static string $resource = OrderResource::class;
}
