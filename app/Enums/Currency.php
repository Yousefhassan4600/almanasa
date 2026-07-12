<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum Currency: string
{
    use HasOptions;

    case Egp = 'EGP';
    case Usd = 'USD';
    case Eur = 'EUR';
    case Sar = 'SAR';
    case Aed = 'AED';

    public function label(): string
    {
        return $this->value;
    }
}
