<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Courses\CheckCourseSubscription;
use App\Actions\StudentPortal\Lessons\CalculateAssessmentAttempts;
use App\Actions\StudentPortal\Lessons\ManageLessonVideoPlayback;
use App\Actions\StudentPortal\Lessons\ResolveLessonItem;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;

class LessonPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Url(as: 'item')]
    public ?int $itemId = null;

    private ResolveLessonItem $resolveLessonItem;

    private CalculateAssessmentAttempts $calculateAssessmentAttempts;

    private ManageLessonVideoPlayback $manageLessonVideoPlayback;

    private CheckCourseSubscription $checkCourseSubscription;

    public function boot(
        ResolveLessonItem $resolveLessonItem,
        CalculateAssessmentAttempts $calculateAssessmentAttempts,
        ManageLessonVideoPlayback $manageLessonVideoPlayback,
        CheckCourseSubscription $checkCourseSubscription,
    ): void {
        $this->resolveLessonItem = $resolveLessonItem;
        $this->calculateAssessmentAttempts = $calculateAssessmentAttempts;
        $this->manageLessonVideoPlayback = $manageLessonVideoPlayback;
        $this->checkCourseSubscription = $checkCourseSubscription;
    }

    public function mount(): void
    {
        $this->itemId ??= request()->integer('item') ?: null;
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $lessonItem = $this->resolveLessonItem->handle($provider, $this->itemId);
        $hasCourseSubscription = $lessonItem?->lesson?->course
            ? $this->checkCourseSubscription->handle($lessonItem->lesson->course, Auth::id())
            : false;
        $videoPlayback = $this->manageLessonVideoPlayback->resolve(
            $lessonItem,
            Auth::id(),
            $hasCourseSubscription,
        );

        return view('livewire.website.lesson-page', [
            'provider' => $provider,
            'lessonItem' => $lessonItem,
            'lessonItems' => $lessonItem?->lesson?->items ?? collect(),
            'hasCourseSubscription' => $hasCourseSubscription,
            'signedVideoUrl' => $videoPlayback['signedVideoUrl'],
            'attempts' => $this->calculateAssessmentAttempts->handle($lessonItem?->assignment ?? $lessonItem?->exam, Auth::id()),
            'studentVideoProgress' => $videoPlayback['studentVideoProgress'],
            'videoViewLimitReached' => $videoPlayback['videoViewLimitReached'],
            'completedVideoWatchCount' => $videoPlayback['completedVideoWatchCount'],
        ]);
    }

    /**
     * @return array{progressId: int|null, progressPercentage: int, lastPositionSeconds: int, watchedSeconds: int, completedWatchCount: int, viewLimit: int|null, viewLimitReached: bool}
     */
    #[Renderless]
    public function saveVideoProgress(int $lessonItemId, int|float $positionSeconds, int|float $durationSeconds, int|float $watchedDeltaSeconds, bool $ended = false, ?int $progressId = null): array
    {
        $provider = Provider::query()->findOrFail($this->providerId);

        return $this->manageLessonVideoPlayback->saveProgress(
            $provider,
            $lessonItemId,
            Auth::id(),
            $positionSeconds,
            $durationSeconds,
            $watchedDeltaSeconds,
            $ended,
            $progressId,
        );
    }
}
