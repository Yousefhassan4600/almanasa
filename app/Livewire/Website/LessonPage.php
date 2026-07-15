<?php

namespace App\Livewire\Website;

use App\Models\LessonItem;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
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
        ]);
    }

    private function lessonItem(Provider $provider): ?LessonItem
    {
        return LessonItem::query()
            ->with([
                'assignment:id,title,description,duration_minutes,max_score',
                'exam:id,title,description,duration_minutes,max_score',
                'lesson' => fn ($query) => $query
                    ->with([
                        'coursePeriod:id,type,name,sort_order',
                        'course:id,provider_id,account_subject_id,academy_teacher_id,title',
                        'course.academyTeacher.teacher.owner:id,first_name,last_name',
                        'course.accountSubject.gradeSubject.grade:id,education_stage_id,name',
                        'course.accountSubject.gradeSubject.subject:id,track_id,name',
                        'course.accountSubject.gradeSubject.subject.track:id,name',
                        'items' => fn ($query) => $query
                            ->where('is_active', true)
                            ->oldest('sort_order')
                            ->oldest('id'),
                    ]),
            ])
            ->where('is_active', true)
            ->whereHas(
                'lesson.course',
                fn (Builder $query): Builder => $query->whereBelongsTo($provider),
            )
            ->when($this->itemId, fn (Builder $query): Builder => $query->whereKey($this->itemId))
            ->first();
    }
}
