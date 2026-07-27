<?php

namespace App\Livewire\Website;

use App\Enums\LessonTypeEnum;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Exam;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\StudentAttempt;
use App\Models\StudentVideoProgress;
use App\Models\Subscription;
use App\Services\BunnyStreamService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;

class LessonPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'item')]
    public ?int $itemId = null;

    public function mount(): void
    {
        $this->itemId ??= request()->integer('item') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $lessonItem = $this->lessonItem($provider);
        $hasCourseSubscription = $lessonItem?->lesson?->course
            ? $this->hasActiveCourseSubscription($lessonItem->lesson->course)
            : false;
        $canWatchVideo = $this->canWatchVideo($lessonItem, $hasCourseSubscription);
        $videoViewLimitReached = $this->videoViewLimitReached($lessonItem);
        $studentVideoProgress = $canWatchVideo && ! $videoViewLimitReached
            ? $this->currentStudentVideoProgress($lessonItem, createWhenAvailable: true)
            : $this->latestStudentVideoProgress($lessonItem);

        return view('livewire.website.lesson-page', [
            'provider' => $provider,
            'lessonItem' => $lessonItem,
            'lessonItems' => $lessonItem?->lesson?->items ?? collect(),
            'hasCourseSubscription' => $hasCourseSubscription,
            'signedVideoUrl' => $this->signedVideoUrl($lessonItem, $hasCourseSubscription, $videoViewLimitReached, app(BunnyStreamService::class)),
            'attempts' => $this->attempts($lessonItem?->assignment ?? $lessonItem?->exam),
            'studentVideoProgress' => $studentVideoProgress,
            'videoViewLimitReached' => $videoViewLimitReached,
            'completedVideoWatchCount' => $this->completedVideoWatchCount($lessonItem),
        ]);
    }

    /**
     * @return array{progressId: int|null, progressPercentage: int, lastPositionSeconds: int, watchedSeconds: int, completedWatchCount: int, viewLimit: int|null, viewLimitReached: bool}
     */
    #[Renderless]
    public function saveVideoProgress(int $lessonItemId, int|float $positionSeconds, int|float $durationSeconds, int|float $watchedDeltaSeconds, bool $ended = false, ?int $progressId = null): array
    {
        if (! Auth::check()) {
            return $this->videoProgressPayload(null, null);
        }

        $provider = Provider::query()->findOrFail($this->providerId);
        $lessonItem = $this->progressLessonItem($provider, $lessonItemId);

        if (! $lessonItem || $lessonItem->type !== LessonTypeEnum::Video) {
            return $this->videoProgressPayload(null, null);
        }

        $course = $lessonItem->lesson?->course;
        $lesson = $lessonItem->lesson;

        if (! $course || ! $lesson) {
            return $this->videoProgressPayload(null, null);
        }

        if (! $lesson->isCurrentlyOpen() || ! $lessonItem->isCurrentlyOpen()) {
            return $this->videoProgressPayload($this->latestStudentVideoProgress($lessonItem), $lessonItem);
        }

        if (! $lessonItem->is_free && ! $this->hasActiveCourseSubscription($course)) {
            return $this->videoProgressPayload($this->latestStudentVideoProgress($lessonItem), $lessonItem);
        }

        $progress = $progressId
            ? $this->studentVideoProgressById($lessonItem, $progressId)
            : $this->currentStudentVideoProgress($lessonItem, createWhenAvailable: true);

        if (! $progress) {
            return $this->videoProgressPayload($this->latestStudentVideoProgress($lessonItem), $lessonItem);
        }

        $duration = $this->resolvedVideoDurationSeconds($lessonItem, $durationSeconds);
        $position = max(0, min((int) ceil($positionSeconds), $duration));
        $delta = max(0, min((int) ceil($watchedDeltaSeconds), 10));

        $watchedSeconds = min(
            max(0, (int) $progress->watched_seconds) + $delta,
            $duration,
        );
        $progressPercentage = $this->progressPercentage($watchedSeconds, $duration);
        $completionPercentage = $this->completionWatchPercentage($provider);
        $isCompleted = $progressPercentage >= $completionPercentage || $ended;

        $progress->fill([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'duration_seconds' => $duration,
            'watched_seconds' => $watchedSeconds,
            'last_position_seconds' => $position,
            'progress_percentage' => $progressPercentage,
            'completed_at' => $isCompleted && blank($progress->completed_at) ? now() : $progress->completed_at,
            'last_watched_at' => now(),
        ])->save();

        return $this->videoProgressPayload($progress->refresh(), $lessonItem);
    }

    private function lessonItem(Provider $provider): ?LessonItem
    {
        return LessonItem::query()
            ->with([
                'assignment:id,course_id,title,description,duration_minutes,num_of_questions,num_of_attempts',
                'exam:id,course_id,title,description,duration_minutes,max_degree,num_of_questions,num_of_attempts',
                'lesson' => fn ($query) => $query
                    ->with([
                        'coursePeriod:id,type,name,sort_order',
                        'course:id,provider_id,account_subject_id,academy_teacher_id,title',
                        'course.provider.owner:id,first_name,last_name',
                        'course.academyTeacher.teacher.owner:id,first_name,last_name',
                        'course.accountSubject.gradeSubject.grade:id,education_stage_id,name',
                        'course.accountSubject.gradeSubject:id,grade_id,track_id,subject_id',
                        'course.accountSubject.gradeSubject.track:id,name',
                        'course.accountSubject.gradeSubject.subject:id,name',
                        'items' => fn ($query) => $query
                            ->with('exam:id,course_id,title,duration_minutes')
                            ->oldest('sort_order')
                            ->oldest('id'),
                    ]),
            ])
            ->whereHas(
                'lesson.course',
                fn (Builder $query): Builder => $query->whereBelongsTo($provider),
            )
            ->when($this->itemId, fn (Builder $query): Builder => $query->whereKey($this->itemId))
            ->first();
    }

    /**
     * @return array{limit: int|null, used: int, remaining: int|null}
     */
    private function attempts(Assignment|Exam|null $assessment): array
    {
        $limit = (int) ($assessment?->num_of_attempts ?? 0);
        $limit = $limit > 0 ? $limit : null;

        if (! $assessment || ! Auth::check()) {
            return [
                'limit' => $limit,
                'used' => 0,
                'remaining' => $limit,
            ];
        }

        $used = StudentAttempt::query()
            ->where('student_user_id', Auth::id())
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas(
                'currentStatus.type',
                fn (Builder $query): Builder => $query->whereIn('slug', ['submitted', 'graded']),
            )
            ->count();

        return [
            'limit' => $limit,
            'used' => $used,
            'remaining' => $limit === null ? null : max(0, $limit - $used),
        ];
    }

    private function hasActiveCourseSubscription(Course $course): bool
    {
        $studentUserId = Auth::id();

        if (! $studentUserId) {
            return false;
        }

        return Subscription::query()
            ->activeForStudentCourse($studentUserId, $course)
            ->exists();
    }

    private function signedVideoUrl(?LessonItem $lessonItem, bool $hasCourseSubscription, bool $videoViewLimitReached, BunnyStreamService $bunnyStream): ?string
    {
        if (! Auth::check()) {
            return null;
        }

        if (! $lessonItem || $lessonItem->type !== LessonTypeEnum::Video) {
            return null;
        }

        if ($videoViewLimitReached) {
            return null;
        }

        $videoReference = $lessonItem->bunny_video_id ?: $lessonItem->video_url;

        if (blank($videoReference)) {
            return null;
        }

        if (! ($lessonItem->lesson?->isCurrentlyOpen() ?? false) || ! $lessonItem->isCurrentlyOpen()) {
            return null;
        }

        if (! $lessonItem->is_free && ! $hasCourseSubscription) {
            return null;
        }

        try {
            return $bunnyStream->signedEmbedUrl($videoReference, $this->videoTokenTtlSeconds($lessonItem));
        } catch (RuntimeException $exception) {
            report($exception);

            return null;
        }
    }

    private function videoTokenTtlSeconds(LessonItem $lessonItem): int
    {
        $durationSeconds = (int) ($lessonItem->duration_seconds ?? 0);

        if ($durationSeconds <= 0) {
            return 3600;
        }

        return $durationSeconds + 7200;
    }

    private function progressLessonItem(Provider $provider, int $lessonItemId): ?LessonItem
    {
        return LessonItem::query()
            ->with([
                'lesson.course',
                'lesson.course.provider',
            ])
            ->whereKey($lessonItemId)
            ->whereHas(
                'lesson.course',
                fn (Builder $query): Builder => $query->whereBelongsTo($provider),
            )
            ->first();
    }

    private function currentStudentVideoProgress(?LessonItem $lessonItem, bool $createWhenAvailable = false): ?StudentVideoProgress
    {
        if (! $lessonItem || ! Auth::check()) {
            return null;
        }

        $latestProgress = $this->latestStudentVideoProgress($lessonItem);

        if ($latestProgress && blank($latestProgress->completed_at)) {
            return $latestProgress;
        }

        if (! $createWhenAvailable || $this->videoViewLimitReached($lessonItem)) {
            return $latestProgress;
        }

        $lesson = $lessonItem->lesson;
        $course = $lesson?->course;

        if (! $lesson || ! $course) {
            return null;
        }

        return StudentVideoProgress::query()->create([
            'student_user_id' => Auth::id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'lesson_item_id' => $lessonItem->id,
            'watch_number' => ((int) ($latestProgress?->watch_number ?? 0)) + 1,
            'duration_seconds' => (int) ($lessonItem->duration_seconds ?? 0),
            'last_watched_at' => now(),
        ]);
    }

    private function latestStudentVideoProgress(?LessonItem $lessonItem): ?StudentVideoProgress
    {
        if (! $lessonItem || ! Auth::check()) {
            return null;
        }

        return StudentVideoProgress::query()
            ->where('student_user_id', Auth::id())
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->latest('id')
            ->first();
    }

    private function studentVideoProgressById(LessonItem $lessonItem, int $progressId): ?StudentVideoProgress
    {
        return StudentVideoProgress::query()
            ->whereKey($progressId)
            ->where('student_user_id', Auth::id())
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->first();
    }

    private function videoViewLimit(?LessonItem $lessonItem): ?int
    {
        $viewLimit = (int) ($lessonItem?->lesson?->num_of_video_views ?? 0);

        return $viewLimit > 0 ? $viewLimit : null;
    }

    private function videoViewLimitReached(?LessonItem $lessonItem): bool
    {
        $viewLimit = $this->videoViewLimit($lessonItem);

        return filled($viewLimit)
            && $this->completedVideoWatchCount($lessonItem) >= $viewLimit;
    }

    /**
     * @return array{progressId: int|null, progressPercentage: int, lastPositionSeconds: int, watchedSeconds: int, completedWatchCount: int, viewLimit: int|null, viewLimitReached: bool}
     */
    private function videoProgressPayload(?StudentVideoProgress $progress, ?LessonItem $lessonItem): array
    {
        return [
            'progressId' => $progress?->id,
            'progressPercentage' => (int) ($progress?->progress_percentage ?? 0),
            'lastPositionSeconds' => (int) ($progress?->last_position_seconds ?? 0),
            'watchedSeconds' => (int) ($progress?->watched_seconds ?? 0),
            'completedWatchCount' => $this->completedVideoWatchCount($lessonItem),
            'viewLimit' => $this->videoViewLimit($lessonItem),
            'viewLimitReached' => $this->videoViewLimitReached($lessonItem),
        ];
    }

    private function resolvedVideoDurationSeconds(LessonItem $lessonItem, int|float $durationSeconds): int
    {
        $storedDuration = (int) ($lessonItem->duration_seconds ?? 0);
        $reportedDuration = (int) ceil($durationSeconds);

        return max(1, $storedDuration, $reportedDuration);
    }

    private function completionWatchPercentage(Provider $provider): int
    {
        $completionPercentage = (int) ($provider->completion_watch_percentage ?: 70);

        return min(100, max(1, $completionPercentage));
    }

    private function progressPercentage(int $watchedSeconds, int $durationSeconds): int
    {
        if ($durationSeconds <= 0) {
            return 0;
        }

        return min(100, (int) floor(($watchedSeconds / $durationSeconds) * 100));
    }

    private function completedVideoWatchCount(?LessonItem $lessonItem): int
    {
        if (! $lessonItem || ! Auth::check()) {
            return 0;
        }

        return StudentVideoProgress::query()
            ->where('student_user_id', Auth::id())
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->whereNotNull('completed_at')
            ->count();
    }

    private function canWatchVideo(?LessonItem $lessonItem, bool $hasCourseSubscription): bool
    {
        if (! Auth::check()) {
            return false;
        }

        if (! $lessonItem || $lessonItem->type !== LessonTypeEnum::Video) {
            return false;
        }

        if (! ($lessonItem->lesson?->isCurrentlyOpen() ?? false) || ! $lessonItem->isCurrentlyOpen()) {
            return false;
        }

        return $lessonItem->is_free || $hasCourseSubscription;
    }
}
