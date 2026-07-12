<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum LessonContentType: string
{
    use HasOptions;

    case RecordedVideo = 'recorded_video';
    case Pdf = 'pdf';
    case Text = 'text';
    case Image = 'image';
    case Attachment = 'attachment';
    case Homework = 'homework';
    case Quiz = 'quiz';
    case ExternalLink = 'external_link';
}
