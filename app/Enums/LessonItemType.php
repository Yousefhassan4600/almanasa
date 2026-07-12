<?php

namespace App\Enums;

enum LessonItemType: string
{
    case Video = 'video';
    case Pdf = 'pdf';
    case Assignment = 'assignment';
    case Exam = 'exam';

    public static function options(): array
    {
        return [
            self::Video->value => 'Video',
            self::Pdf->value => 'Pdf',
            self::Assignment->value => 'Assignment',
            self::Exam->value => 'Exam',
        ];
    }
}
