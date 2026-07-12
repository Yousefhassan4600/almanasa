<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Base\Pages\BaseCreateRecord;
use App\Filament\Resources\Payments\PaymentResource;

class CreatePayment extends BaseCreateRecord
{
    protected static string $resource = PaymentResource::class;
}
