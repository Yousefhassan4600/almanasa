<?php

namespace App\Actions\Assignments;

use App\Enums\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Validation\ValidationException;

class GenerateAssignmentQuestions
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function handle(array $data): array
    {
        $courseId = (int) ($data['course_id'] ?? 0);
        $totalQuestions = (int) ($data['num_of_questions'] ?? 0);
        $lessonIds = collect($data['lesson_ids'] ?? [])
            ->filter(fn (mixed $lessonId): bool => filled($lessonId))
            ->map(fn (mixed $lessonId): int => (int) $lessonId)
            ->values()
            ->all();

        if (! $courseId || $totalQuestions <= 0) {
            $data['question_ids'] = [];

            return $data;
        }

        $difficultyCounts = [
            QuestionDifficulty::Easy->value => (int) ($data['num_of_easy_questions'] ?? 0),
            QuestionDifficulty::Medium->value => (int) ($data['num_of_medium_questions'] ?? 0),
            QuestionDifficulty::Hard->value => (int) ($data['num_of_hard_questions'] ?? 0),
        ];

        if (array_sum($difficultyCounts) > $totalQuestions) {
            throw ValidationException::withMessages([
                'num_of_questions' => 'The difficulty question counts cannot be greater than the total number of questions.',
            ]);
        }

        $questionIds = [];

        foreach ($difficultyCounts as $difficulty => $count) {
            if ($count <= 0) {
                continue;
            }

            $questionIds = [
                ...$questionIds,
                ...$this->randomQuestionIds($courseId, $lessonIds, $count, $difficulty, $questionIds),
            ];
        }

        $remainingCount = $totalQuestions - count($questionIds);

        if ($remainingCount > 0) {
            $questionIds = [
                ...$questionIds,
                ...$this->randomQuestionIds($courseId, $lessonIds, $remainingCount, null, $questionIds),
            ];
        }

        if (count($questionIds) < $totalQuestions) {
            throw ValidationException::withMessages([
                'num_of_questions' => 'There are not enough questions in this course to generate the assignment.',
            ]);
        }

        $data['question_ids'] = $questionIds;

        return $data;
    }

    /**
     * @param  array<int, int>  $excludedQuestionIds
     * @param  array<int, int>  $lessonIds
     * @return array<int, int>
     */
    private function randomQuestionIds(int $courseId, array $lessonIds, int $count, ?string $difficulty, array $excludedQuestionIds): array
    {
        return Question::query()
            ->whereHas('lesson', fn ($query) => $query->where('course_id', $courseId))
            ->when($lessonIds !== [], fn ($query) => $query->whereIn('lesson_id', $lessonIds))
            ->when($difficulty, fn ($query) => $query->where('difficulty', $difficulty))
            ->when($excludedQuestionIds !== [], fn ($query) => $query->whereNotIn('id', $excludedQuestionIds))
            ->inRandomOrder()
            ->limit($count)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }
}
