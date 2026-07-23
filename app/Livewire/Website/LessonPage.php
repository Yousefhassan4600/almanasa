<?php

namespace App\Livewire\Website;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Exam;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\StudentAttempt;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

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

        return view('livewire.website.lesson-page', [
            'provider' => $provider,
            'lessonItem' => $lessonItem,
            'lessonItems' => $lessonItem?->lesson?->items ?? collect(),
            'hasCourseSubscription' => $lessonItem?->lesson?->course
                ? $this->hasActiveCourseSubscription($lessonItem->lesson->course)
                : false,
            'attempts' => $this->attempts($lessonItem?->assignment ?? $lessonItem?->exam),
        ]);
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
}
