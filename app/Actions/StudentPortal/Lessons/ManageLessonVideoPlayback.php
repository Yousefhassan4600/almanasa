<?php

namespace App\Actions\StudentPortal\Lessons;

use App\Enums\LessonTypeEnum;
use App\Models\Course;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\StudentVideoProgress;
use App\Models\Subscription;
use App\Services\BunnyStreamService;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class ManageLessonVideoPlayback
{
    public function __construct(private BunnyStreamService $bunnyStream) {}

    /**
     * @return array{studentVideoProgress: StudentVideoProgress|null, videoViewLimitReached: bool, completedVideoWatchCount: int, signedVideoUrl: string|null}
     */
    public function resolve(?LessonItem $lessonItem, ?int $studentUserId, bool $hasCourseSubscription): array
    {
        $canWatchVideo = $this->canWatchVideo($lessonItem, $studentUserId, $hasCourseSubscription);
        $videoViewLimitReached = $this->videoViewLimitReached($lessonItem, $studentUserId);
        $studentVideoProgress = $canWatchVideo && ! $videoViewLimitReached
            ? $this->currentStudentVideoProgress($lessonItem, $studentUserId, createWhenAvailable: true)
            : $this->latestStudentVideoProgress($lessonItem, $studentUserId);

        return [
            'studentVideoProgress' => $studentVideoProgress,
            'videoViewLimitReached' => $videoViewLimitReached,
            'completedVideoWatchCount' => $this->completedVideoWatchCount($lessonItem, $studentUserId),
            'signedVideoUrl' => $this->signedVideoUrl($lessonItem, $studentUserId, $hasCourseSubscription, $videoViewLimitReached),
        ];
    }

    /**
     * @return array{progressId: int|null, progressPercentage: int, lastPositionSeconds: int, watchedSeconds: int, completedWatchCount: int, viewLimit: int|null, viewLimitReached: bool}
     */
    public function saveProgress(
        Provider $provider,
        int $lessonItemId,
        ?int $studentUserId,
        int|float $positionSeconds,
        int|float $durationSeconds,
        int|float $watchedDeltaSeconds,
        bool $ended = false,
        ?int $progressId = null,
    ): array {
        if (! $studentUserId) {
            return $this->videoProgressPayload(null, null, null);
        }

        $lessonItem = $this->progressLessonItem($provider, $lessonItemId);

        if (! $lessonItem || $lessonItem->type !== LessonTypeEnum::Video) {
            return $this->videoProgressPayload(null, null, $studentUserId);
        }

        $course = $lessonItem->lesson?->course;
        $lesson = $lessonItem->lesson;

        if (! $course || ! $lesson) {
            return $this->videoProgressPayload(null, null, $studentUserId);
        }

        if (! $lesson->isCurrentlyOpen() || ! $lessonItem->isCurrentlyOpen()) {
            return $this->videoProgressPayload(
                $this->latestStudentVideoProgress($lessonItem, $studentUserId),
                $lessonItem,
                $studentUserId,
            );
        }

        if (! $lessonItem->is_free && ! $this->hasActiveCourseSubscription($course, $studentUserId)) {
            return $this->videoProgressPayload(
                $this->latestStudentVideoProgress($lessonItem, $studentUserId),
                $lessonItem,
                $studentUserId,
            );
        }

        $progress = $progressId
            ? $this->studentVideoProgressById($lessonItem, $studentUserId, $progressId)
            : $this->currentStudentVideoProgress($lessonItem, $studentUserId, createWhenAvailable: true);

        if (! $progress) {
            return $this->videoProgressPayload(
                $this->latestStudentVideoProgress($lessonItem, $studentUserId),
                $lessonItem,
                $studentUserId,
            );
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

        return $this->videoProgressPayload($progress->refresh(), $lessonItem, $studentUserId);
    }

    private function signedVideoUrl(
        ?LessonItem $lessonItem,
        ?int $studentUserId,
        bool $hasCourseSubscription,
        bool $videoViewLimitReached,
    ): ?string {
        if (! $this->canWatchVideo($lessonItem, $studentUserId, $hasCourseSubscription) || $videoViewLimitReached) {
            return null;
        }

        $videoReference = $lessonItem?->bunny_video_id ?: $lessonItem?->video_url;

        if (blank($videoReference)) {
            return null;
        }

        try {
            return $this->bunnyStream->signedEmbedUrl($videoReference, $this->videoTokenTtlSeconds($lessonItem));
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

    private function currentStudentVideoProgress(?LessonItem $lessonItem, ?int $studentUserId, bool $createWhenAvailable = false): ?StudentVideoProgress
    {
        if (! $lessonItem || ! $studentUserId) {
            return null;
        }

        $latestProgress = $this->latestStudentVideoProgress($lessonItem, $studentUserId);

        if ($latestProgress && blank($latestProgress->completed_at)) {
            return $latestProgress;
        }

        if (! $createWhenAvailable || $this->videoViewLimitReached($lessonItem, $studentUserId)) {
            return $latestProgress;
        }

        $lesson = $lessonItem->lesson;
        $course = $lesson?->course;

        if (! $lesson || ! $course) {
            return null;
        }

        return StudentVideoProgress::query()->create([
            'student_user_id' => $studentUserId,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'lesson_item_id' => $lessonItem->id,
            'watch_number' => ((int) ($latestProgress?->watch_number ?? 0)) + 1,
            'duration_seconds' => (int) ($lessonItem->duration_seconds ?? 0),
            'last_watched_at' => now(),
        ]);
    }

    private function latestStudentVideoProgress(?LessonItem $lessonItem, ?int $studentUserId): ?StudentVideoProgress
    {
        if (! $lessonItem || ! $studentUserId) {
            return null;
        }

        return StudentVideoProgress::query()
            ->where('student_user_id', $studentUserId)
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->latest('id')
            ->first();
    }

    private function studentVideoProgressById(LessonItem $lessonItem, int $studentUserId, int $progressId): ?StudentVideoProgress
    {
        return StudentVideoProgress::query()
            ->whereKey($progressId)
            ->where('student_user_id', $studentUserId)
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->first();
    }

    private function videoViewLimit(?LessonItem $lessonItem): ?int
    {
        $viewLimit = (int) ($lessonItem?->lesson?->num_of_video_views ?? 0);

        return $viewLimit > 0 ? $viewLimit : null;
    }

    private function videoViewLimitReached(?LessonItem $lessonItem, ?int $studentUserId): bool
    {
        $viewLimit = $this->videoViewLimit($lessonItem);

        return filled($viewLimit)
            && $this->completedVideoWatchCount($lessonItem, $studentUserId) >= $viewLimit;
    }

    /**
     * @return array{progressId: int|null, progressPercentage: int, lastPositionSeconds: int, watchedSeconds: int, completedWatchCount: int, viewLimit: int|null, viewLimitReached: bool}
     */
    private function videoProgressPayload(?StudentVideoProgress $progress, ?LessonItem $lessonItem, ?int $studentUserId): array
    {
        return [
            'progressId' => $progress?->id,
            'progressPercentage' => (int) ($progress?->progress_percentage ?? 0),
            'lastPositionSeconds' => (int) ($progress?->last_position_seconds ?? 0),
            'watchedSeconds' => (int) ($progress?->watched_seconds ?? 0),
            'completedWatchCount' => $this->completedVideoWatchCount($lessonItem, $studentUserId),
            'viewLimit' => $this->videoViewLimit($lessonItem),
            'viewLimitReached' => $this->videoViewLimitReached($lessonItem, $studentUserId),
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

    private function completedVideoWatchCount(?LessonItem $lessonItem, ?int $studentUserId): int
    {
        if (! $lessonItem || ! $studentUserId) {
            return 0;
        }

        return StudentVideoProgress::query()
            ->where('student_user_id', $studentUserId)
            ->whereBelongsTo($lessonItem, 'lessonItem')
            ->whereNotNull('completed_at')
            ->count();
    }

    private function hasActiveCourseSubscription(Course $course, int $studentUserId): bool
    {
        return Subscription::query()
            ->activeForStudentCourse($studentUserId, $course)
            ->exists();
    }

    private function canWatchVideo(?LessonItem $lessonItem, ?int $studentUserId, bool $hasCourseSubscription): bool
    {
        if (! $studentUserId) {
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
