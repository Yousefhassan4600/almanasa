<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CourseStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Published = 'published';
    case Archived = 'archived';
    case Suspended = 'suspended';
}
