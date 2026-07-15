<?php

namespace App\Enums;

enum LessonTypeEnum: string
{
    case Video = 'video';
    case File = 'file';
    case Link = 'link';
    case Assignment = 'assignment';
    case Exam = 'exam';

    public static function options(): array
    {
        return [
            self::Video->value => 'Video',
            self::File->value => 'File',
            self::Link->value => 'Link',
            self::Assignment->value => 'Assignment',
            self::Exam->value => 'Exam',
        ];
    }
}
