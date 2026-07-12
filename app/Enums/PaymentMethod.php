<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card = 'card';
    case Instapay = 'instapay';
    case VodafoneCash = 'vodafone_cash';
    case OrangeCash = 'orange_cash';
    case EtisalatCash = 'etisalat_cash';
    case Code = 'code';
    case BankTransfer = 'bank_transfer';
    case Wallet = 'wallet';

    public static function options(): array
    {
        return [
            self::Card->value => 'Card',
            self::Instapay->value => 'Instapay',
            self::VodafoneCash->value => 'Vodafone Cash',
            self::OrangeCash->value => 'Orange Cash',
            self::EtisalatCash->value => 'Etisalat Cash',
            self::Code->value => 'Code',
            self::BankTransfer->value => 'Bank Transfer',
            self::Wallet->value => 'Wallet',
        ];
    }
}
