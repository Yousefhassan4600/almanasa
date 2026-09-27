<?php

namespace App\Http\Resources;

use App\Actions\StudentPortal\Lessons\ManageLessonVideoPlayback;
use App\Enums\LessonTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleTeacherPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $teacher = $this['teacher'];
        $course = $this['course'];
        $coursePeriodId = $this['coursePeriodId'] ?? null;
        $hasCourseSubscription = $this['hasCourseSubscription'] ?? false;
        $lessonCodeIds = $this['lessonCodeIds'] ?? [];
        $userId = $this['userId'] ?? null;

        $teacherOwner = $teacher?->teacher?->owner ?? $teacher?->owner;

        $periods = $course?->lessons
            ->pluck('coursePeriod')
            ->filter()
            ->unique('id')
            ->map(fn ($period) => [
                'id' => $period->id,
                'name' => $period->name,
            ])
            ->values();

        $lessons = $course?->lessons;
        if ($coursePeriodId && $lessons) {
            $lessons = $lessons->filter(fn ($lesson) => $lesson->course_period_id == $coursePeriodId);
        }

        $manageVideoPlayback = app(ManageLessonVideoPlayback::class);

        return [
            'teacher' => [
                'id' => $teacher?->id,
                'name' => trim(($teacherOwner?->first_name ?? '').' '.($teacherOwner?->last_name ?? '')),
                'avatar' => $this->storageUrl($teacher?->image ?? $teacherOwner?->avatar),
                'bio' => $teacher?->bio,
                'rating' => $teacher?->rating ?? 5,
            ],
            'monthly_price' => $this['monthlyPrice'],
            'selected_subject_id' => $this['selectedSubjectId'],
            'selected_period_id' => $coursePeriodId,
            'has_subscription' => $hasCourseSubscription,
            'periods' => $periods ?? [],

            'course' => $course ? [
                'id' => $course->id,
                'title' => $course->title,
                'lessons' => $lessons?->map(function ($lesson) use ($hasCourseSubscription, $lessonCodeIds, $userId, $manageVideoPlayback) {
                    $hasLessonAccess = $hasCourseSubscription || in_array($lesson->id, $lessonCodeIds, true);
                    $lessonIsOpen = $lesson->isCurrentlyOpen() || in_array($lesson->id, $lessonCodeIds, true);

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'period_id' => $lesson->course_period_id,
                        'period_name' => $lesson->coursePeriod?->name,
                        'items' => $lesson->items->map(function ($item) use ($hasLessonAccess, $lessonIsOpen, $userId, $manageVideoPlayback) {

                            $isSubscribedOrFree = (bool) $item->is_free || $hasLessonAccess;

                            $isOpen = method_exists($item, 'isCurrentlyOpen') ? $item->isCurrentlyOpen() : true;

                            $videoLimitReached = false;
                            $isVideo = $item->type === LessonTypeEnum::Video || ! empty($item->video_url) || ! empty($item->bunny_video_id);

                            if ($isVideo && $userId) {
                                $playback = $manageVideoPlayback->resolve($item, $userId, $hasLessonAccess);
                                $videoLimitReached = (bool) ($playback['videoViewLimitReached'] ?? false);
                            }

                            $isLocked = ! $isSubscribedOrFree || ! $isOpen || ! $lessonIsOpen || $videoLimitReached;

                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'type' => $item->type instanceof LessonTypeEnum ? $item->type->value : $item->type,
                                'is_free' => (bool) $item->is_free,
                                'is_locked' => $isLocked,
                                'video_limit_reached' => $videoLimitReached,
                            ];
                        }),
                    ];
                })->values(),
            ] : null,
        ];
    }

    private function storageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
