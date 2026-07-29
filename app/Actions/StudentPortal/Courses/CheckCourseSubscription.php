<?php

namespace App\Actions\StudentPortal\Courses;

use App\Models\Course;
use App\Models\Subscription;

class CheckCourseSubscription
{
    public function handle(Course $course, ?int $studentUserId): bool
    {
        if (! $studentUserId) {
            return false;
        }

        return Subscription::query()
            ->activeForStudentCourse($studentUserId, $course)
            ->exists();
    }
}
