<?php

namespace App\Actions\StudentPortal\Students;

use App\Models\Provider;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

class LoadMyLessons
{
    /**
     * @return array{student: User|null, studentProfile: mixed, subscriptions: Collection<int, Subscription>}
     */
    public function handle(Provider $provider, ?User $student): array
    {
        return [
            'student' => $student,
            'studentProfile' => $student?->studentProfile()->with('grade')->first(),
            'subscriptions' => $student ? $this->subscriptions($provider, $student->id) : collect(),
        ];
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
                'course.accountSubject.gradeSubject:id,grade_id,track_id,subject_id',
                'course.accountSubject.gradeSubject.grade:id,name',
                'course.accountSubject.gradeSubject.track:id,name',
                'course.accountSubject.gradeSubject.subject:id,name,icon',
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
