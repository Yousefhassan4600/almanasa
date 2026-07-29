<?php

namespace App\Actions\StudentPortal\Lessons;

use App\Models\Assignment;
use App\Models\Exam;
use App\Models\StudentAttempt;
use Illuminate\Database\Eloquent\Builder;

class CalculateAssessmentAttempts
{
    /**
     * @return array{limit: int|null, used: int, remaining: int|null}
     */
    public function handle(Assignment|Exam|null $assessment, ?int $studentUserId): array
    {
        $limit = (int) ($assessment?->num_of_attempts ?? 0);
        $limit = $limit > 0 ? $limit : null;

        if (! $assessment || ! $studentUserId) {
            return [
                'limit' => $limit,
                'used' => 0,
                'remaining' => $limit,
            ];
        }

        $used = StudentAttempt::query()
            ->where('student_user_id', $studentUserId)
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
}
