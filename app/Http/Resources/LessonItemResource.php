<?php

namespace App\Http\Resources;

use App\Enums\LessonTypeEnum;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lessonItem            = $this['lessonItem'];
         $hasCourseSubscription = $this['hasCourseSubscription'] ?? false;
        $videoPlayback         = $this['videoPlayback'] ?? null;
        $attempts              = $this['attempts'] ?? null;

        $lesson = $lessonItem?->lesson;

        $nextItem = $lesson?->items()->where('sort_order', '>', $lessonItem->sort_order)->oldest('sort_order')->first();
        $prevItem = $lesson?->items()->where('sort_order', '<', $lessonItem->sort_order)->latest('sort_order')->first();

        $typeValue = $lessonItem->type instanceof LessonTypeEnum ? $lessonItem->type->value : $lessonItem->type;

        $response = [
            'id'                      => $lessonItem->id,
            'title'                   => $lessonItem->title,
            'description'             => $lessonItem->description,
            'type'                    => $typeValue,
            'is_free'                 => (bool) $lessonItem->is_free,
            'is_active'               => (bool) $lessonItem->is_active,
             'has_course_subscription' => $hasCourseSubscription,
            'navigation'              => [
                'previous_item_id' => $prevItem?->id,
                'next_item_id'     => $nextItem?->id,
            ],
        ];

        // 1. الفيديو
        if ($lessonItem->type === LessonTypeEnum::Video || ! empty($videoPlayback) || ! empty($lessonItem->video_url)) {
            $response['video'] = [
                'signed_video_url'            => $videoPlayback['signedVideoUrl'] ?? $this->storageUrl($lessonItem->video_url),
                'bunny_video_id'              => $lessonItem->bunny_video_id,
                'student_video_progress'      => $videoPlayback['studentVideoProgress'] ?? 0,
                'video_view_limit_reached'    => (bool) ($videoPlayback['videoViewLimitReached'] ?? false),
                'completed_video_watch_count' => $videoPlayback['completedVideoWatchCount'] ?? 0,
                'duration_seconds'            => $lessonItem->duration_seconds ?? 0,
            ];
        }

        // 2. الملفات والروابط
        if (! empty($lessonItem->link_url) || ! empty($lessonItem->file_url) || in_array($lessonItem->type, [LessonTypeEnum::File ?? null, LessonTypeEnum::Link ?? null])) {
            $response['file'] = [
                'file_url' => $this->storageUrl($lessonItem->file_url),
                'link_url' => $lessonItem->link_url,
            ];
        }

        // 3. الامتحانات والواجبات
        $assignment = $lessonItem->assignment;
        $exam       = $lessonItem->exam;
        $assessment = $assignment ?? $exam;

        if ($assessment) {
            if ($assignment) {
                $ids = $assignment->question_ids;
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                $ids = is_array($ids) ? array_values(array_filter($ids)) : [];

                $questions = ! empty($ids)
                    ? Question::withoutGlobalScopes()->whereIn('id', $ids)->with('options')->get()
                    : collect();
            } else {
                $questions = $exam->courseQuestions()->with('options')->get();
            }

            $response['assessment'] = [
                'id'               => $assessment->id,
                'title'            => $assessment->title,
                'description'      => $assessment->description,
                'type'             => $assignment ? 'assignment' : 'exam',
                'duration_minutes' => $assessment->duration_minutes ?? 0,
                'num_of_questions' => $assessment->num_of_questions ?? $questions->count(),
                'max_degree'       => $assessment->max_degree ?? null,
                'attempts'         => $attempts,
                'questions'        => $questions->map(function ($question, $index) {
                    return [
                        'id'              => $question->id,
                        'question_number' => $index + 1,
                        'question_text'   => $question->title,
                        'media'           => $this->storageUrl($question->media),
                        'difficulty'      => $question->difficulty?->value ?? $question->difficulty,
                        'degree_text'     => __('ten_degrees'),
                        'options'         => $question->options?->map(function ($option) {
                            return [
                                'id'         => $option->id,
                                'text'       => $option->title ?? $option->text,
                                'code'       => $option->code,
                                'is_correct' => (bool) ($option->is_correct ?? false),
                            ];
                        }),
                    ];
                }),
            ];
        }

        return $response;
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
