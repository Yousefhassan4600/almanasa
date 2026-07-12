<?php

namespace App\Filament\Resources\PaymentCodes\Pages;

use App\Filament\Resources\PaymentCodes\PaymentCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePaymentCodes extends ManageRecords
{
    protected static string $resource = PaymentCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
