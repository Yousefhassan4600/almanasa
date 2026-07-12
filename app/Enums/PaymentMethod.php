<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PaymentMethod: string
{
    use HasOptions;

    case Card = 'card';
    case InstaPay = 'instapay';
    case VodafoneCash = 'vodafone_cash';
    case OrangeCash = 'orange_cash';
    case EtisalatCash = 'etisalat_cash';
    case Wallet = 'wallet';
    case PaymentCode = 'payment_code';
    case BankTransfer = 'bank_transfer';
    case ManualTransfer = 'manual_transfer';
}
