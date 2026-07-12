<?php

namespace App\Filament\Resources\PaymentCodes\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\PaymentCodes\PaymentCodeResource;

class ListPaymentCodes extends BaseListRecords
{
    protected static string $resource = PaymentCodeResource::class;
}
