<?php

namespace App\Actions\StudentPortal\Attempts;

use App\Models\Provider;
use App\Models\StudentAttempt;
use Illuminate\Database\Eloquent\Builder;

class LoadAttemptResult
{
    public function handle(Provider $provider, ?int $studentUserId, ?int $attemptId): ?StudentAttempt
    {
        if (! $attemptId || ! $studentUserId) {
            return null;
        }

        return StudentAttempt::query()
            ->with([
                'attemptable',
                'course:id,provider_id,account_subject_id,academy_teacher_id,title',
                'currentStatus.type',
                'statuses.type',
                'examModel',
                'studentAnswers' => fn ($query) => $query->oldest('id'),
                'studentAnswers.question.options',
                'studentAnswers.question_option',
            ])
            ->whereKey($attemptId)
            ->where('student_user_id', $studentUserId)
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }
}
