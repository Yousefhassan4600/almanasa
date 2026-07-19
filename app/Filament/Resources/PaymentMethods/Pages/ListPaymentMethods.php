<?php

namespace App\Filament\Resources\PaymentMethods\Pages;

use App\Filament\Base\Pages\BaseListRecords;
use App\Filament\Resources\PaymentMethods\PaymentMethodResource;

class ListPaymentMethods extends BaseListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    public function hasCreateAction(): bool
    {
        return false;
    }
}
