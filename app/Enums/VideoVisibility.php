<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum VideoVisibility: string
{
    use HasOptions;

    case Private = 'private';
    case Preview = 'preview';
    case Public = 'public';
}
