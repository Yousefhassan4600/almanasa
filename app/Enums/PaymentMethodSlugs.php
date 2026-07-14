<?php

namespace App\Enums;

enum PaymentMethodSlugs: string
{
    case Bank = 'bank-transfer';
    case InstaPay = 'insta-pay';
    case VodafoneCash = 'vodafone-cash';
    case OrangeCash = 'orange-cash';
    case ECash = 'e&-cash';
    case Code = 'code';
}
