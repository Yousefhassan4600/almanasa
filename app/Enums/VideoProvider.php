<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum VideoProvider: string
{
    use HasOptions;

    case BunnyStream = 'bunny_stream';
    case Vimeo = 'vimeo';
    case YouTube = 'youtube';
    case Local = 'local';
    case Other = 'other';
}
