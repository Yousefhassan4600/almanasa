<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleTeacherPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $teacher = $this['teacher'];
        $course = $this['course'];
        $coursePeriodId = $this['coursePeriodId'] ?? null;

        $teacherOwner = $teacher?->teacher?->owner ?? $teacher?->owner;

        $periods = $course?->lessons
            ->pluck('coursePeriod')
            ->filter()
            ->unique('id')
            ->map(fn ($period) => [
                'id'   => $period->id,
                'name' => $period->name,
            ])
            ->values();

        $lessons = $course?->lessons;
        if ($coursePeriodId && $lessons) {
            $lessons = $lessons->filter(fn ($lesson) => $lesson->course_period_id == $coursePeriodId);
        }

        return [
            'teacher' => [
                'id'     => $teacher?->id,
                'name'   => trim(($teacherOwner?->first_name ?? '') . ' ' . ($teacherOwner?->last_name ?? '')),
                'avatar' => $this->storageUrl($teacher?->image ?? $teacherOwner?->avatar),
                'bio'    => $teacher?->bio ,
                'rating' => $teacher?->rating ?? 5,
            ],
            'has_subscription'    => $this['hasCourseSubscription'],
            'monthly_price'       => $this['monthlyPrice'],
            'selected_subject_id' => $this['selectedSubjectId'],
            'selected_period_id'  => $coursePeriodId,

            'periods' => $periods ?? [],

            'course' => $course ? [
                'id'      => $course->id,
                'title'   => $course->title,
                'lessons' => $lessons?->map(function ($lesson) {
                    return [
                        'id'          => $lesson->id,
                        'title'       => $lesson->title,
                        'period_id'   => $lesson->course_period_id,
                        'period_name' => $lesson->coursePeriod?->name,
                        'items'       => $lesson->items->map(function ($item) {
                            return [
                                'id'      => $item->id,
                                'title'   => $item->title,
                                'type'    => $item->type,
                                'is_free' => (bool) $item->is_free,
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

        return asset('storage/' . ltrim($path, '/'));
    }
}
