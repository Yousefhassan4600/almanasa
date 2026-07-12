<?php

namespace App\Filament\Resources\CartItems\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\CartItems\CartItemResource;

class ListCartItems extends BaseListRecords
{
    protected static string $resource = CartItemResource::class;
}
