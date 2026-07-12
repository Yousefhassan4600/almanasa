<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PublishingStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
