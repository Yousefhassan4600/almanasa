<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\Payments\PaymentResource;

class ListPayments extends BaseListRecords
{
    protected static string $resource = PaymentResource::class;
}
