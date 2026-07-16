<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\AttemptStatusType;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Exam;
use App\Models\ExamModel;
use App\Models\LessonProgress;
use App\Models\LessonProgressStatus;
use App\Models\LessonProgressStatusType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentAnswer;
use App\Models\StudentAttempt;
use App\Models\User;
use Carbon\CarbonInterface;

class StudentActivitySeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->attemptStatusTypes();
        $this->lessonProgressStatusTypes();
        $this->lessonProgresses();
        $this->courseReviews();
        $this->studentAttempts();
    }

    private function attemptStatusTypes(): void
    {
        foreach (
            [
                'in_progress' => ['In Progress', 'قيد الحل', 1],
                'submitted' => ['Submitted', 'تم التسليم', 2],
                'graded' => ['Graded', 'تم التصحيح', 3],
            ] as $slug => [$nameEn, $nameAr, $sortOrder]
        ) {
            AttemptStatusType::query()->updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $this->translation($nameEn, $nameAr),
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }

    private function lessonProgressStatusTypes(): void
    {
        foreach (
            [
                'in_progress' => ['In Progress', 'قيد المشاهدة', 1],
                'completed' => ['Completed', 'مكتمل', 2],
            ] as $slug => [$nameEn, $nameAr, $sortOrder]
        ) {
            LessonProgressStatusType::query()->updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $this->translation($nameEn, $nameAr),
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }

    private function studentAttempts(): void
    {
        $student = User::query()->where('phone', '01000000004')->firstOrFail();
        $assignment = Assignment::query()
            ->where('title->en', 'Homework')
            ->with('course')
            ->firstOrFail();
        $exam = Exam::query()
            ->where('title->en', 'Lesson Exam')
            ->with(['course', 'models'])
            ->firstOrFail();

        $this->studentAttempt(
            student: $student,
            attemptable: $assignment,
            questionIds: $assignment->question_ids ?? [],
            correctQuestionIds: $assignment->question_ids ?? [],
            startedAt: now()->subDays(2)->setTime(18, 0),
            submittedAt: now()->subDays(2)->setTime(18, 18),
            maxScore: 3,
            examModel: null,
        );

        $examModel = $exam->models()->oldest('model_number')->first();
        $examQuestionIds = $examModel?->questionIdList()->all() ?? [];

        $this->studentAttempt(
            student: $student,
            attemptable: $exam,
            questionIds: $examQuestionIds,
            correctQuestionIds: collect($examQuestionIds)->take(2)->all(),
            startedAt: now()->subDay()->setTime(19, 0),
            submittedAt: now()->subDay()->setTime(19, 16),
            maxScore: 10,
            examModel: $examModel,
        );
    }

    private function lessonProgresses(): void
    {
        $student = User::query()->where('phone', '01000000004')->firstOrFail();
        $course = Course::query()
            ->where('title->en', 'Welcome to the Mathematics Course')
            ->with('lessons')
            ->firstOrFail();
        $lesson = $course->lessons()->oldest('sort_order')->firstOrFail();
        $startedAt = now()->subDays(5)->setTime(17, 0);
        $lastWatchedAt = now()->subDays(4)->setTime(17, 45);

        $progress = LessonProgress::query()->updateOrCreate([
            'student_user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ], [
            'course_id' => $course->id,
        ]);

        $this->lessonProgressStatus(
            progress: $progress,
            slug: 'in_progress',
            isCurrent: false,
            statusAt: $startedAt->copy()->addMinutes(5),
            notes: 'Student started watching lesson content.'
        );
        $this->lessonProgressStatus(
            progress: $progress,
            slug: 'completed',
            isCurrent: true,
            statusAt: $lastWatchedAt,
            notes: 'Student completed all required lesson watch time.'
        );
    }

    private function courseReviews(): void
    {
        $student = User::query()->where('phone', '01000000004')->firstOrFail();
        $course = Course::query()
            ->where('title->en', 'Welcome to the Mathematics Course')
            ->firstOrFail();

        CourseReview::query()->updateOrCreate([
            'student_user_id' => $student->id,
            'course_id' => $course->id,
        ], [
            'rating' => 5,
            'comment' => 'The explanations are clear, and the homework helps me practice after each lesson.',
            'is_approved' => true,
        ]);
    }

    /**
     * @param  array<int, int>  $questionIds
     * @param  array<int, int>  $correctQuestionIds
     */
    private function studentAttempt(
        User $student,
        Assignment|Exam $attemptable,
        array $questionIds,
        array $correctQuestionIds,
        CarbonInterface $startedAt,
        CarbonInterface $submittedAt,
        int $maxScore,
        ?ExamModel $examModel,
    ): StudentAttempt {
        $correctQuestionIds = collect($correctQuestionIds)
            ->map(fn(mixed $questionId): int => (int) $questionId)
            ->all();

        $attempt = StudentAttempt::query()->updateOrCreate([
            'student_user_id' => $student->id,
            'course_id' => $attemptable->course_id,
            'attemptable_type' => $attemptable::class,
            'attemptable_id' => $attemptable->id,
            'attempt_number' => 1,
        ], [
            'max_score' => $maxScore,
            'exam_model_id' => $examModel?->id,
        ]);

        $this->attemptStatus($attempt, 'in_progress', false, $startedAt);
        $this->studentAnswers($attempt, $questionIds, $correctQuestionIds);
        $hasManualAnswers = $attempt->studentAnswers()
            ->whereHas('question', fn($query) => $query->where('type', QuestionType::Statement->value))
            ->whereNull('score')
            ->exists();

        $this->attemptStatus($attempt, 'submitted', $hasManualAnswers, $submittedAt);

        if (! $hasManualAnswers) {
            $this->attemptStatus($attempt, 'graded', true, $submittedAt->copy()->addMinutes(2));
        }

        return $attempt;
    }

    private function attemptStatus(StudentAttempt $attempt, string $slug, bool $isCurrent, CarbonInterface $statusAt): void
    {
        $statusType = AttemptStatusType::query()->where('slug', $slug)->firstOrFail();

        $attempt->statuses()->updateOrCreate([
            'attempt_status_type_id' => $statusType->id,
        ], [
            'is_current' => $isCurrent,
            'status_at' => $statusAt,
        ]);
    }

    private function lessonProgressStatus(
        LessonProgress $progress,
        string $slug,
        bool $isCurrent,
        CarbonInterface $statusAt,
        ?string $notes = null,
    ): void {
        $statusType = LessonProgressStatusType::query()->where('slug', $slug)->firstOrFail();

        LessonProgressStatus::query()->updateOrCreate([
            'lesson_progress_id' => $progress->id,
            'lesson_progress_status_type_id' => $statusType->id,
        ], [
            'is_current' => $isCurrent,
            'notes' => $notes,
            'status_at' => $statusAt,
        ]);
    }

    /**
     * @param  array<int, int>  $questionIds
     * @param  array<int, int>  $correctQuestionIds
     */
    private function studentAnswers(StudentAttempt $attempt, array $questionIds, array $correctQuestionIds): void
    {
        foreach ($questionIds as $questionId) {
            $question = Question::query()->with('options')->findOrFail($questionId);
            $requiresManualGrading = $question->type === QuestionType::Statement;
            $isCorrect = in_array((int) $questionId, $correctQuestionIds, true);
            $option = $this->answerOption($question, $isCorrect);
            $questionMaxScore = $this->questionMaxScore($attempt, (int) $questionId, count($questionIds));

            StudentAnswer::query()->updateOrCreate([
                'student_attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ], [
                'question_option_id' => $option?->id,
                'answer_text' => $option ? null : $this->answerText($isCorrect),
                'is_correct' => $requiresManualGrading ? null : $isCorrect,
                'score' => $requiresManualGrading ? null : ($isCorrect ? round($questionMaxScore, 2) : 0),
            ]);
        }
    }

    private function questionMaxScore(StudentAttempt $attempt, int $questionId, int $questionsCount): float
    {
        if ($attempt->attemptable_type === Exam::class) {
            $examModel = $attempt->examModel;

            if ($examModel) {
                $maxScore = $examModel->questionMaxScore($questionId);

                if ($maxScore !== null) {
                    return (float) $maxScore;
                }
            }
        }

        if ($questionsCount === 0) {
            return 0;
        }

        return (float) ($attempt->max_score ?? 0) / $questionsCount;
    }

    private function answerOption(Question $question, bool $isCorrect): ?QuestionOption
    {
        if ($question->options->isEmpty()) {
            return null;
        }

        return $question->options
            ->where('is_correct', $isCorrect)
            ->sortBy('sort_order')
            ->first();
    }

    private function answerText(bool $isCorrect): string
    {
        return $isCorrect
            ? 'Rational numbers can be written as fractions, while irrational numbers cannot be written as exact fractions.'
            : 'They are the same type of number.';
    }
}
