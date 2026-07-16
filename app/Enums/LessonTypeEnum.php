<?php

namespace App\Enums;

enum LessonTypeEnum: string
{
    case Video = 'video';
    case File = 'file';
    case Link = 'link';
    case Assignments = 'assignments';
    case Exams = 'exams';

    public static function options(): array
    {
        return [
            self::Video->value => 'Video',
            self::File->value => 'File',
            self::Link->value => 'Link',
            self::Assignments->value => 'Assignments',
            self::Exams->value => 'Exams',
        ];
    }
}
