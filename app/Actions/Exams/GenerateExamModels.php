<?php

namespace App\Actions\Exams;

use App\Enums\QuestionDifficulty;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenerateExamModels
{
    public function handle(Exam $exam): void
    {
        $totalQuestions = (int) ($exam->num_of_questions ?? 0);
        $numOfModels = (int) ($exam->num_of_models ?? 1);

        if ($totalQuestions <= 0 || $numOfModels <= 0) {
            $exam->models()->delete();

            return;
        }

        DB::transaction(function () use ($exam, $totalQuestions, $numOfModels): void {
            for ($modelNumber = 1; $modelNumber <= $numOfModels; $modelNumber++) {
                $exam->models()->updateOrCreate([
                    'model_number' => $modelNumber,
                ], [
                    'question_ids' => $this->generateQuestionIds($exam, $totalQuestions),
                ]);
            }

            $exam->models()
                ->where('model_number', '>', $numOfModels)
                ->delete();
        });
    }

    /**
     * @return array<int, int>
     */
    private function generateQuestionIds(Exam $exam, int $totalQuestions): array
    {
        $difficultyCounts = [
            QuestionDifficulty::Easy->value => (int) ($exam->num_of_easy_questions ?? 0),
            QuestionDifficulty::Medium->value => (int) ($exam->num_of_medium_questions ?? 0),
            QuestionDifficulty::Hard->value => (int) ($exam->num_of_hard_questions ?? 0),
        ];

        if (array_sum($difficultyCounts) > $totalQuestions) {
            throw ValidationException::withMessages([
                'num_of_questions' => 'The difficulty question counts cannot be greater than the total number of questions.',
            ]);
        }

        $questionIds = [];
        $lessonIds = $this->lessonIds($exam);

        foreach ($difficultyCounts as $difficulty => $count) {
            if ($count <= 0) {
                continue;
            }

            $questionIds = [
                ...$questionIds,
                ...$this->randomQuestionIds($exam->course_id, $lessonIds, $count, $difficulty, $questionIds),
            ];
        }

        $remainingCount = $totalQuestions - count($questionIds);

        if ($remainingCount > 0) {
            $questionIds = [
                ...$questionIds,
                ...$this->randomQuestionIds($exam->course_id, $lessonIds, $remainingCount, null, $questionIds),
            ];
        }

        if (count($questionIds) < $totalQuestions) {
            throw ValidationException::withMessages([
                'num_of_questions' => 'There are not enough questions in this course to generate the exam model.',
            ]);
        }

        return $questionIds;
    }

    /**
     * @return array<int, int>
     */
    private function lessonIds(Exam $exam): array
    {
        return collect($exam->lesson_ids ?? [])
            ->filter(fn (mixed $lessonId): bool => filled($lessonId))
            ->map(fn (mixed $lessonId): int => (int) $lessonId)
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $lessonIds
     * @param  array<int, int>  $excludedQuestionIds
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
