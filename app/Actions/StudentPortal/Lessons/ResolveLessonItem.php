<?php

namespace App\Actions\StudentPortal\Lessons;

use App\Models\LessonItem;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;

class ResolveLessonItem
{
    public function handle(Provider $provider, ?int $lessonItemId): ?LessonItem
    {
        return LessonItem::query()
            ->with([
                'assignment:id,course_id,title,description,duration_minutes,num_of_questions,num_of_attempts,question_ids',
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
            ->when($lessonItemId, fn (Builder $query): Builder => $query->whereKey($lessonItemId))
            ->first();
    }
}
