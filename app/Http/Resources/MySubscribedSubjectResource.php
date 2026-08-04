<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MySubscribedSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $course = $this->course;
        $accountSubject = $course?->accountSubject;
        $gradeSubject = $accountSubject?->gradeSubject;
        $subject = $gradeSubject?->subject;
        $track = $gradeSubject?->track;

        $teacherOwner = $course?->academyTeacher?->teacher?->owner
            ?? $course?->provider?->owner;

        $teacherImage = $course?->academyTeacher?->image;
     $teacher = $course?->academyTeacher;

        return [
            'subscription_id' => $this->id,
            'course' => [
                'id' => $course?->id,
                'title' => $course?->title,
                'thumbnail' => $course?->thumbnail,
            ],
            'subject' => [
                'id' => $subject?->id,
                'name' => $subject?->name,
                'icon' => $subject?->icon,
                'track_name' => $track?->name,
            ],
            'teacher' => [
                'id'    => $teacher?->id,
                'name' => trim(($teacherOwner?->first_name ?? '') . ' ' . ($teacherOwner?->last_name ?? '')),
                'image' =>  $this->storageUrl($this->teacherImage),
                'title' => 'مدرس ' . ($subject?->name ?? ''),
            ],

            'progress_percentage' => $this->progress_percentage ?? 0,
            'last_watched_lesson' => [
                'id' => $this->last_watched_lesson_id ?? null,
                'title' => $this->last_watched_lesson_title ?? __('no_recently_watched_lessons'),
            ],
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
