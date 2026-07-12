<?php

namespace App\Filament\Resources\PaymentProofs\Pages;

use App\Filament\Resources\PaymentProofs\PaymentProofResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentProof extends EditRecord
{
    protected static string $resource = PaymentProofResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
