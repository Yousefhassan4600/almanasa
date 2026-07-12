<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AssessmentType: string
{
    use HasOptions;

    case Homework = 'homework';
    case Quiz = 'quiz';
    case PracticeTest = 'practice_test';
    case LessonExam = 'lesson_exam';
    case CourseExam = 'course_exam';
    case FinalExam = 'final_exam';
}
