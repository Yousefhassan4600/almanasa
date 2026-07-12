<?php

namespace App\Filament\Resources\PaymentCodes\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\PaymentCodes\PaymentCodeResource;

class CreatePaymentCode extends BaseCreateRecord
{
    protected static string $resource = PaymentCodeResource::class;
}
