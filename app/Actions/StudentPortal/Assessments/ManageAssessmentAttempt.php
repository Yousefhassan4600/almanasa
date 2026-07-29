<?php

namespace App\Actions\StudentPortal\Assessments;

use App\Enums\QuestionType;
use App\Models\Assignment;
use App\Models\AttemptStatusType;
use App\Models\Exam;
use App\Models\ExamModel;
use App\Models\LessonItem;
use App\Models\Provider;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentAttempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ManageAssessmentAttempt
{
    public function start(int $providerId, string $type, ?int $assignmentId, ?int $examId, ?int $itemId, bool $retry, ?int $studentUserId): void
    {
        if (! $studentUserId) {
            return;
        }

        $provider = Provider::query()->find($providerId);

        if (! $provider) {
            return;
        }

        $assessment = $this->assessment($provider, $type, $assignmentId, $examId);

        if (! $assessment || ! $this->isOpen($provider, $assessment, $itemId) || $this->hasReachedAttemptLimit($assessment, $studentUserId)) {
            return;
        }

        if (! $retry && $this->latestSubmittedAttempt($assessment, $studentUserId)) {
            return;
        }

        $questions = $this->questions($assessment, $studentUserId);

        if ($questions->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($assessment, $questions, $studentUserId): void {
            $this->ensureStatusTypes();

            $examModel = $assessment instanceof Exam
                ? $this->examModel($assessment, $studentUserId)
                : null;
            $inProgressAttempt = $this->inProgressAttempt($assessment, $studentUserId);

            if ($inProgressAttempt) {
                $this->finalizeAttempt($assessment, $questions, $examModel, $inProgressAttempt, [], true);

                return;
            }

            $attempt = $this->attempt($assessment, $questions, $examModel, $studentUserId);

            $this->createBlankAnswers($attempt, $questions);
        });
    }

    public function questionsCount(Provider $provider, string $type, ?int $assignmentId, ?int $examId, ?int $studentUserId): int
    {
        $assessment = $this->assessment($provider, $type, $assignmentId, $examId);

        return $assessment ? $this->questions($assessment, $studentUserId)->count() : 0;
    }

    /**
     * @param  array<int|string, mixed>  $answers
     * @return array{attempt: StudentAttempt|null, redirectAttempt: StudentAttempt|null, errors: array<string, string>}
     */
    public function submit(
        Provider $provider,
        string $type,
        ?int $assignmentId,
        ?int $examId,
        ?int $itemId,
        ?int $studentUserId,
        array $answers,
        bool $force = false,
    ): array {
        $assessment = $this->assessment($provider, $type, $assignmentId, $examId);

        if (! $studentUserId) {
            return [
                'attempt' => null,
                'redirectAttempt' => null,
                'errors' => ['auth' => 'يجب تسجيل الدخول أولاً.'],
            ];
        }

        if (! $assessment || ! $this->isOpen($provider, $assessment, $itemId)) {
            return [
                'attempt' => null,
                'redirectAttempt' => null,
                'errors' => ['assessment' => 'هذا الاختبار أو الواجب غير متاح حالياً.'],
            ];
        }

        $existingAttempt = $this->latestSubmittedAttempt($assessment, $studentUserId);

        if ($existingAttempt && $this->hasReachedAttemptLimit($assessment, $studentUserId)) {
            return [
                'attempt' => null,
                'redirectAttempt' => $existingAttempt,
                'errors' => [],
            ];
        }

        $questions = $this->questions($assessment, $studentUserId);

        if ($questions->isEmpty()) {
            return [
                'attempt' => null,
                'redirectAttempt' => null,
                'errors' => ['assessment' => 'لا توجد أسئلة متاحة حالياً.'],
            ];
        }

        if (! $force) {
            $errors = $this->requiredAnswerErrors($questions, $answers);

            if ($errors !== []) {
                return [
                    'attempt' => null,
                    'redirectAttempt' => null,
                    'errors' => $errors,
                ];
            }
        }

        $attempt = DB::transaction(function () use ($assessment, $force, $questions, $studentUserId, $answers): StudentAttempt {
            $this->ensureStatusTypes();

            $examModel = $assessment instanceof Exam
                ? $this->examModel($assessment, $studentUserId)
                : null;
            $attempt = $this->attempt($assessment, $questions, $examModel, $studentUserId);

            return $this->finalizeAttempt($assessment, $questions, $examModel, $attempt, $answers, $force);
        });

        return [
            'attempt' => $attempt,
            'redirectAttempt' => null,
            'errors' => [],
        ];
    }

    /**
     * @return array{assessment: Assignment|Exam|null, questions: Collection<int, Question>, isOpen: bool, existingAttempt: StudentAttempt|null, resultUrl: string|null, retryUrl: string|null, canRetry: bool, assessmentType: string, currentQuestion: Question|null, remainingSeconds: int|null}
     */
    public function renderData(Provider $provider, string $type, ?int $assignmentId, ?int $examId, ?int $itemId, bool $retry, int $currentQuestionIndex, ?int $studentUserId): array
    {
        $assessment = $this->assessment($provider, $type, $assignmentId, $examId);
        $questions = $assessment ? $this->questions($assessment, $studentUserId) : collect();
        $currentQuestionIndex = max(0, min($currentQuestionIndex, max(0, $questions->count() - 1)));
        $existingAttempt = $assessment && $studentUserId
            ? $this->latestSubmittedAttempt($assessment, $studentUserId)
            : null;
        $hasReachedAttemptLimit = $assessment && $studentUserId
            ? $this->hasReachedAttemptLimit($assessment, $studentUserId)
            : false;
        $isOpen = $assessment ? $this->isOpen($provider, $assessment, $itemId) : false;
        $canRetry = $assessment && $existingAttempt && ! $hasReachedAttemptLimit && $isOpen;
        $shouldShowExistingAttempt = $existingAttempt && ($hasReachedAttemptLimit || ! $retry);
        $activeAttempt = $assessment && $studentUserId
            ? $this->inProgressAttempt($assessment, $studentUserId)
            : null;

        return [
            'assessment' => $assessment,
            'questions' => $questions,
            'isOpen' => $isOpen,
            'existingAttempt' => $shouldShowExistingAttempt ? $existingAttempt : null,
            'resultUrl' => $existingAttempt ? $this->resultUrl($type, $existingAttempt) : null,
            'retryUrl' => $assessment ? $this->retryUrl($assessment, $itemId) : null,
            'canRetry' => $canRetry,
            'assessmentType' => $type,
            'currentQuestion' => $questions->get($currentQuestionIndex),
            'remainingSeconds' => $assessment ? $this->remainingSeconds($assessment, $activeAttempt) : null,
        ];
    }

    public function resultUrl(string $type, StudentAttempt $attempt): string
    {
        $path = $type === 'exam' ? '/quiz_done' : '/home_work_done';

        return "{$path}?attempt={$attempt->id}";
    }

    private function assessment(Provider $provider, string $type, ?int $assignmentId, ?int $examId): Assignment|Exam|null
    {
        return $type === 'exam'
            ? $this->exam($provider, $examId)
            : $this->assignment($provider, $assignmentId);
    }

    private function assignment(Provider $provider, ?int $assignmentId): ?Assignment
    {
        if (! $assignmentId) {
            return null;
        }

        return Assignment::query()
            ->with('course:id,provider_id,title')
            ->whereKey($assignmentId)
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }

    private function exam(Provider $provider, ?int $examId): ?Exam
    {
        if (! $examId) {
            return null;
        }

        return Exam::query()
            ->with(['course:id,provider_id,title', 'models'])
            ->whereKey($examId)
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }

    private function isOpen(Provider $provider, Assignment|Exam $assessment, ?int $itemId): bool
    {
        $lessonItem = $this->assessmentLessonItem($provider, $assessment, $itemId);

        if (! $lessonItem) {
            return blank($itemId);
        }

        return $lessonItem->isCurrentlyOpen();
    }

    private function assessmentLessonItem(Provider $provider, Assignment|Exam $assessment, ?int $itemId): ?LessonItem
    {
        return LessonItem::query()
            ->whereHas(
                'lesson.course',
                fn (Builder $query): Builder => $query->whereBelongsTo($provider),
            )
            ->when(
                $assessment instanceof Exam,
                fn (Builder $query): Builder => $query->where('exam_id', $assessment->id),
                fn (Builder $query): Builder => $query->where('assignment_id', $assessment->id),
            )
            ->when($itemId, fn (Builder $query): Builder => $query->whereKey($itemId))
            ->oldest('sort_order')
            ->oldest('id')
            ->first();
    }

    private function hasReachedAttemptLimit(Assignment|Exam $assessment, int $studentUserId): bool
    {
        $limit = $this->attemptLimit($assessment);

        if ($limit === null) {
            return false;
        }

        return $this->submittedAttemptsCount($assessment, $studentUserId) >= $limit;
    }

    private function attemptLimit(Assignment|Exam $assessment): ?int
    {
        $limit = $assessment->num_of_attempts;

        if (blank($limit) || (int) $limit <= 0) {
            return null;
        }

        return (int) $limit;
    }

    private function submittedAttemptsCount(Assignment|Exam $assessment, int $studentUserId): int
    {
        return StudentAttempt::query()
            ->where('student_user_id', $studentUserId)
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas(
                'currentStatus.type',
                fn (Builder $query): Builder => $query->whereIn('slug', ['submitted', 'graded']),
            )
            ->count();
    }

    /**
     * @return Collection<int, Question>
     */
    private function questions(Assignment|Exam $assessment, ?int $studentUserId): Collection
    {
        $questionIds = $assessment instanceof Exam
            ? $this->examModel($assessment, $studentUserId)?->questionIdList()->all() ?? []
            : array_map('intval', $assessment->question_ids ?? []);

        if ($questionIds === []) {
            return new Collection;
        }

        return Question::query()
            ->with(['options' => fn ($query) => $query->oldest('sort_order')->oldest('id')])
            ->whereIn('id', $questionIds)
            ->get()
            ->sortBy(fn (Question $question): int => array_search($question->id, $questionIds, true))
            ->values();
    }

    private function examModel(Exam $exam, ?int $studentUserId): ?ExamModel
    {
        $existingAttempt = $studentUserId
            ? $this->latestSubmittedAttempt($exam, $studentUserId)
            : null;

        if ($existingAttempt?->examModel) {
            return $existingAttempt->examModel;
        }

        if ($exam->relationLoaded('models')) {
            return $exam->models->sortBy('model_number')->first();
        }

        return $exam->models()->oldest('model_number')->first();
    }

    private function latestSubmittedAttempt(Assignment|Exam $assessment, int $studentUserId): ?StudentAttempt
    {
        return StudentAttempt::query()
            ->with(['examModel', 'currentStatus.type'])
            ->where('student_user_id', $studentUserId)
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas(
                'currentStatus.type',
                fn (Builder $query): Builder => $query->whereIn('slug', ['submitted', 'graded']),
            )
            ->latest('attempt_number')
            ->latest('id')
            ->first();
    }

    private function inProgressAttempt(Assignment|Exam $assessment, int $studentUserId): ?StudentAttempt
    {
        return StudentAttempt::query()
            ->with(['currentStatus.type'])
            ->where('student_user_id', $studentUserId)
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas('currentStatus.type', fn (Builder $query): Builder => $query->where('slug', 'in_progress'))
            ->latest()
            ->first();
    }

    /**
     * @param  Collection<int, Question>  $questions
     */
    private function attempt(Assignment|Exam $assessment, Collection $questions, ?ExamModel $examModel, int $studentUserId): StudentAttempt
    {
        $inProgressAttempt = StudentAttempt::query()
            ->where('student_user_id', $studentUserId)
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas('currentStatus.type', fn (Builder $query): Builder => $query->where('slug', 'in_progress'))
            ->latest()
            ->first();

        if ($inProgressAttempt) {
            return $inProgressAttempt;
        }

        $attemptNumber = ((int) StudentAttempt::query()
            ->where('student_user_id', $studentUserId)
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->max('attempt_number')) + 1;

        return StudentAttempt::query()->create([
            'student_user_id' => $studentUserId,
            'course_id' => $assessment->course_id,
            'attemptable_type' => $assessment::class,
            'attemptable_id' => $assessment->id,
            'attempt_number' => $attemptNumber,
            'exam_model_id' => $examModel?->id,
            'max_score' => $this->assessmentMaxScore($assessment, $questions, $examModel),
        ]);
    }

    /**
     * @param  Collection<int, Question>  $questions
     */
    private function assessmentMaxScore(Assignment|Exam $assessment, Collection $questions, ?ExamModel $examModel): float
    {
        if ($assessment instanceof Exam) {
            $modelScore = $examModel?->questionItems()->sum(fn (array $item): float => (float) ($item['max_score'] ?? 0)) ?? 0;

            return round((float) ($assessment->max_degree ?: $modelScore ?: $questions->count()), 2);
        }

        return (float) max(1, $questions->count());
    }

    /**
     * @param  array<int|string, mixed>  $answers
     */
    private function storeAnswer(StudentAttempt $attempt, Question $question, ?ExamModel $examModel, int $questionsCount, array $answers, bool $force = false): void
    {
        $answer = $answers[$question->id] ?? null;
        $questionMaxScore = $this->questionMaxScore($attempt, $question, $examModel, $questionsCount);
        $questionOptionId = null;
        $answerText = null;
        $isCorrect = null;
        $score = null;

        if ($question->type === QuestionType::Statement) {
            $answerText = $this->isBlankAnswer($answer) ? null : trim((string) $answer);

            if ($force && $answerText === null) {
                $isCorrect = false;
                $score = 0;
            }
        } else {
            if ($force && $this->isBlankAnswer($answer)) {
                $isCorrect = false;
                $score = 0;
            } else {
                $option = QuestionOption::query()
                    ->whereBelongsTo($question)
                    ->findOrFail((int) $answer);

                $questionOptionId = $option->id;
                $isCorrect = $option->is_correct;
                $score = $isCorrect ? $questionMaxScore : 0;
            }
        }

        $attempt->studentAnswers()->create([
            'question_id' => $question->id,
            'question_option_id' => $questionOptionId,
            'answer_text' => $answerText,
            'is_correct' => $isCorrect,
            'score' => $score,
        ]);
    }

    /**
     * @param  Collection<int, Question>  $questions
     * @param  array<int|string, mixed>  $answers
     */
    private function finalizeAttempt(Assignment|Exam $assessment, Collection $questions, ?ExamModel $examModel, StudentAttempt $attempt, array $answers, bool $force): StudentAttempt
    {
        $attempt->studentAnswers()->delete();

        foreach ($questions as $question) {
            $this->storeAnswer($attempt, $question, $examModel, $questions->count(), $answers, $force);
        }

        $hasManualAnswers = $attempt->studentAnswers()
            ->whereHas('question', fn (Builder $query): Builder => $query->where('type', QuestionType::Statement->value))
            ->whereNull('score')
            ->exists();

        $this->markStatus($attempt, 'submitted', $hasManualAnswers);

        if (! $hasManualAnswers) {
            $this->markStatus($attempt, 'graded', true);
        }

        return $attempt;
    }

    /**
     * @param  Collection<int, Question>  $questions
     */
    private function createBlankAnswers(StudentAttempt $attempt, Collection $questions): void
    {
        if ($attempt->studentAnswers()->exists()) {
            return;
        }

        foreach ($questions as $question) {
            $attempt->studentAnswers()->create([
                'question_id' => $question->id,
                'question_option_id' => null,
                'answer_text' => null,
                'is_correct' => null,
                'score' => null,
            ]);
        }
    }

    private function questionMaxScore(StudentAttempt $attempt, Question $question, ?ExamModel $examModel, int $questionsCount): float
    {
        if ($attempt->attemptable_type === Exam::class) {
            $maxScore = $examModel?->questionMaxScore((int) $question->id);

            if ($maxScore !== null) {
                return round((float) $maxScore, 2);
            }
        }

        if ($questionsCount === 0) {
            return 0;
        }

        return round((float) ($attempt->max_score ?? 0) / $questionsCount, 2);
    }

    private function markStatus(StudentAttempt $attempt, string $slug, bool $isCurrent): void
    {
        $statusType = AttemptStatusType::query()->where('slug', $slug)->firstOrFail();

        $attempt->statuses()->updateOrCreate([
            'attempt_status_type_id' => $statusType->id,
        ], [
            'is_current' => $isCurrent,
            'status_at' => now(),
        ]);
    }

    private function ensureStatusTypes(): void
    {
        foreach ([
            'in_progress' => ['In Progress', 'قيد الحل', 1],
            'submitted' => ['Submitted', 'تم التسليم', 2],
            'graded' => ['Graded', 'تم التصحيح', 3],
        ] as $slug => [$englishName, $arabicName, $sortOrder]) {
            AttemptStatusType::query()->firstOrCreate([
                'slug' => $slug,
            ], [
                'name' => ['en' => $englishName, 'ar' => $arabicName],
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }
    }

    private function retryUrl(Assignment|Exam $assessment, ?int $itemId): string
    {
        $path = $assessment instanceof Exam ? '/quiz' : '/home_work';
        $parameters = $assessment instanceof Exam
            ? ['exam' => $assessment->id]
            : ['assignment' => $assessment->id];

        if ($itemId) {
            $parameters['item'] = $itemId;
        }

        $parameters['retry'] = 1;

        return $path.'?'.http_build_query($parameters);
    }

    private function remainingSeconds(Assignment|Exam $assessment, ?StudentAttempt $attempt): ?int
    {
        if (! $attempt || blank($assessment->duration_minutes)) {
            return null;
        }

        $endsAt = $attempt->created_at->copy()->addMinutes((int) $assessment->duration_minutes);

        if ($endsAt->isPast()) {
            return 0;
        }

        return (int) now()->diffInSeconds($endsAt);
    }

    /**
     * @param  Collection<int, Question>  $questions
     * @param  array<int|string, mixed>  $answers
     * @return array<string, string>
     */
    private function requiredAnswerErrors(Collection $questions, array $answers): array
    {
        $errors = [];

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;

            if ($this->isBlankAnswer($answer)) {
                $errors["answers.{$question->id}"] = 'هذا السؤال مطلوب.';
            }
        }

        return $errors;
    }

    private function isBlankAnswer(mixed $answer): bool
    {
        return $answer === null || (is_string($answer) && trim($answer) === '');
    }
}
