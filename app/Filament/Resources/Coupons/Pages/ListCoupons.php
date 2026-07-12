<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Coupons\CouponResource;

class ListCoupons extends BaseListRecords
{
    protected static string $resource = CouponResource::class;
}
