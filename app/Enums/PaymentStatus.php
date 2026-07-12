<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PaymentStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case AwaitingReview = 'awaiting_review';
    case Approved = 'approved';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
