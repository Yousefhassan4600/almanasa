<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum OrderStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Failed = 'failed';
}
