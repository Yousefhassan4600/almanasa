<?php

namespace App\Filament\Resources\CartItems\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\CartItems\CartItemResource;

class CreateCartItem extends BaseCreateRecord
{
    protected static string $resource = CartItemResource::class;
}
