<?php

namespace App\Livewire\Website;

use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MyLessonsPage extends Component
{
    #[Locked]
    public int $providerId;

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $student = Auth::user();
        $studentProfile = $student?->studentProfile()->with('grade')->first();

        return view('livewire.website.my-lessons-page', [
            'provider' => $provider,
            'student' => $student,
            'studentProfile' => $studentProfile,
            'subscriptions' => $student ? $this->subscriptions($provider, $student->id) : collect(),
        ]);
    }

    /**
     * @return Collection<int, Subscription>
     */
    private function subscriptions(Provider $provider, int $studentUserId): Collection
    {
        return Subscription::query()
            ->with([
                'purchaseUnit:id,type,name',
                'course:id,provider_id,account_subject_id,academy_teacher_id,title,thumbnail',
                'course.provider:id,owner_user_id,type',
                'course.provider.owner:id,first_name,last_name',
                'course.academyTeacher:id,teacher_account_id,image',
                'course.academyTeacher.teacher:id,owner_user_id',
                'course.academyTeacher.teacher.owner:id,first_name,last_name',
                'course.accountSubject:id,grade_subject_id',
                'course.accountSubject.gradeSubject:id,grade_id,subject_id',
                'course.accountSubject.gradeSubject.grade:id,name',
                'course.accountSubject.gradeSubject.subject:id,track_id,name,icon',
                'course.accountSubject.gradeSubject.subject.track:id,name',
                'course.lessons' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with(['items' => fn ($query) => $query
                        ->where('is_active', true)
                        ->oldest('sort_order')
                        ->oldest('id')])
                    ->oldest('sort_order')
                    ->oldest('id'),
            ])
            ->whereBelongsTo($provider)
            ->where('student_user_id', $studentUserId)
            ->latest('starts_at')
            ->latest('id')
            ->get()
            ->unique('course_id')
            ->values();
    }
}
