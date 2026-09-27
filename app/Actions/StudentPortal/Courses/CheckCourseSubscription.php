<?php

namespace App\Actions\StudentPortal\Courses;

use App\Models\Course;
use App\Models\Lesson;
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

    public function forLesson(Lesson $lesson, ?int $studentUserId): bool
    {
        return $studentUserId && Subscription::query()
            ->activeForStudentLesson($studentUserId, $lesson)
            ->exists();
    }

    public function isImmediateLessonAccess(Lesson $lesson, ?int $studentUserId): bool
    {
        return $studentUserId && Subscription::query()
            ->activeForSpecificLesson($studentUserId, $lesson)
            ->exists();
    }
}
