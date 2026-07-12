<?php

namespace App\Filament\Resources\Carts\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Carts\CartResource;

class ListCarts extends BaseListRecords
{
    protected static string $resource = CartResource::class;
}
