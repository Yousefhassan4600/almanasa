<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PlanType: string
{
    use HasOptions;

    case Course = 'course';
    case Subject = 'subject';
    case GradeSubject = 'grade_subject';
    case Teacher = 'teacher';
    case AllSubjects = 'all_subjects';
    case CustomizedPackage = 'customized_package';
}
