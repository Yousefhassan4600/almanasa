<?php

namespace App\Filament\Resources\PaymentProofs\Pages;

use App\Filament\Resources\PaymentProofs\PaymentProofResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentProof extends ViewRecord
{
    protected static string $resource = PaymentProofResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
