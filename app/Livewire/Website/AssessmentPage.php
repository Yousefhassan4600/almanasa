<?php

namespace App\Livewire\Website;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class AssessmentPage extends Component
{
    #[Locked]
    public int $providerId;

    #[Locked]
    public string $type = 'assignment';

    #[Url(as: 'assignment')]
    public ?int $assignmentId = null;

    #[Url(as: 'exam')]
    public ?int $examId = null;

    #[Url(as: 'item')]
    public ?int $itemId = null;

    #[Url(as: 'retry')]
    public bool $retry = false;

    /**
     * @var array<int|string, mixed>
     */
    public array $answers = [];

    public int $currentQuestionIndex = 0;

    public function mount(int $providerId, string $type = 'assignment'): void
    {
        $this->providerId = $providerId;
        $this->type = $type === 'exam' ? 'exam' : 'assignment';
        $this->assignmentId ??= request()->integer('assignment') ?: null;
        $this->examId ??= request()->integer('exam') ?: null;
        $this->itemId ??= request()->integer('item') ?: null;
        $this->retry = request()->boolean('retry', $this->retry);

        $this->startAssessmentAttempt();
    }

    public function nextQuestion(): void
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $assessment = $this->assessment($provider);
        $questionsCount = $assessment ? $this->questions($assessment)->count() : 0;

        if ($questionsCount === 0) {
            $this->currentQuestionIndex = 0;

            return;
        }

        $this->currentQuestionIndex = min($questionsCount - 1, $this->currentQuestionIndex + 1);
    }

    public function previousQuestion(): void
    {
        $this->currentQuestionIndex = max(0, $this->currentQuestionIndex - 1);
    }

    public function submit(bool $force = false): mixed
    {
        $this->resetErrorBag();

        $provider = Provider::query()->findOrFail($this->providerId);
        $assessment = $this->assessment($provider);

        if (! Auth::check()) {
            return redirect('/login');
        }

        if (! $assessment || ! $this->isOpen($provider, $assessment)) {
            $this->addError('assessment', 'هذا الاختبار أو الواجب غير متاح حالياً.');

            return null;
        }

        $existingAttempt = $this->latestSubmittedAttempt($assessment);

        if ($existingAttempt && $this->hasReachedAttemptLimit($assessment)) {
            return redirect($this->resultUrl($existingAttempt));
        }

        $questions = $this->questions($assessment);

        if ($questions->isEmpty()) {
            $this->addError('assessment', 'لا توجد أسئلة متاحة حالياً.');

            return null;
        }

        if (! $force) {
            foreach ($questions as $question) {
                $answer = $this->answers[$question->id] ?? null;

                if ($this->isBlankAnswer($answer)) {
                    $this->addError("answers.{$question->id}", 'هذا السؤال مطلوب.');
                }
            }

            if ($this->getErrorBag()->isNotEmpty()) {
                return null;
            }
        }

        $attempt = DB::transaction(function () use ($assessment, $force, $questions): StudentAttempt {
            $this->ensureStatusTypes();

            $examModel = $assessment instanceof Exam
                ? $this->examModel($assessment)
                : null;
            $attempt = $this->attempt($assessment, $questions, $examModel);

            return $this->finalizeAttempt($assessment, $questions, $examModel, $attempt, $force);
        });

        return redirect($this->resultUrl($attempt));
    }

    public function render(): mixed
    {
        $provider = Provider::query()->findOrFail($this->providerId);
        $assessment = $this->assessment($provider);
        $questions = $assessment ? $this->questions($assessment) : collect();
        $this->currentQuestionIndex = max(0, min($this->currentQuestionIndex, max(0, $questions->count() - 1)));
        $existingAttempt = $assessment && Auth::check()
            ? $this->latestSubmittedAttempt($assessment)
            : null;
        $hasReachedAttemptLimit = $assessment && Auth::check()
            ? $this->hasReachedAttemptLimit($assessment)
            : false;
        $canRetry = $assessment && $existingAttempt && ! $hasReachedAttemptLimit && $this->isOpen($provider, $assessment);
        $shouldShowExistingAttempt = $existingAttempt && ($hasReachedAttemptLimit || ! $this->retry);
        $activeAttempt = $assessment && Auth::check()
            ? $this->inProgressAttempt($assessment)
            : null;

        return view('livewire.website.assessment-page', [
            'provider' => $provider,
            'assessment' => $assessment,
            'questions' => $questions,
            'isOpen' => $assessment ? $this->isOpen($provider, $assessment) : false,
            'existingAttempt' => $shouldShowExistingAttempt ? $existingAttempt : null,
            'resultUrl' => $existingAttempt ? $this->resultUrl($existingAttempt) : null,
            'retryUrl' => $assessment ? $this->retryUrl($assessment) : null,
            'canRetry' => $canRetry,
            'assessmentType' => $this->type,
            'currentQuestion' => $questions->get($this->currentQuestionIndex),
            'remainingSeconds' => $assessment ? $this->remainingSeconds($assessment, $activeAttempt) : null,
        ]);
    }

    private function startAssessmentAttempt(): void
    {
        if (! Auth::check()) {
            return;
        }

        $provider = Provider::query()->find($this->providerId);

        if (! $provider) {
            return;
        }

        $assessment = $this->assessment($provider);

        if (! $assessment || ! $this->isOpen($provider, $assessment) || $this->hasReachedAttemptLimit($assessment)) {
            return;
        }

        if (! $this->retry && $this->latestSubmittedAttempt($assessment)) {
            return;
        }

        $questions = $this->questions($assessment);

        if ($questions->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($assessment, $questions): void {
            $this->ensureStatusTypes();

            $examModel = $assessment instanceof Exam
                ? $this->examModel($assessment)
                : null;
            $inProgressAttempt = $this->inProgressAttempt($assessment);

            if ($inProgressAttempt) {
                $this->finalizeAttempt($assessment, $questions, $examModel, $inProgressAttempt, true);

                return;
            }

            $attempt = $this->attempt($assessment, $questions, $examModel);

            $this->createBlankAnswers($attempt, $questions);
        });
    }

    private function assessment(Provider $provider): Assignment|Exam|null
    {
        return $this->type === 'exam'
            ? $this->exam($provider)
            : $this->assignment($provider);
    }

    private function assignment(Provider $provider): ?Assignment
    {
        if (! $this->assignmentId) {
            return null;
        }

        return Assignment::query()
            ->with('course:id,provider_id,title')
            ->whereKey($this->assignmentId)
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }

    private function exam(Provider $provider): ?Exam
    {
        if (! $this->examId) {
            return null;
        }

        return Exam::query()
            ->with(['course:id,provider_id,title', 'models'])
            ->whereKey($this->examId)
            ->whereHas('course', fn (Builder $query): Builder => $query->whereBelongsTo($provider))
            ->first();
    }

    private function isOpen(Provider $provider, Assignment|Exam $assessment): bool
    {
        $lessonItem = $this->assessmentLessonItem($provider, $assessment);

        if (! $lessonItem) {
            return blank($this->itemId);
        }

        if (! $lessonItem->is_active) {
            return false;
        }

        if (filled($lessonItem->starts_at) && $lessonItem->starts_at->isFuture()) {
            return false;
        }

        if (filled($lessonItem->ends_at) && $lessonItem->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    private function assessmentLessonItem(Provider $provider, Assignment|Exam $assessment): ?LessonItem
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
            ->when($this->itemId, fn (Builder $query): Builder => $query->whereKey($this->itemId))
            ->oldest('sort_order')
            ->oldest('id')
            ->first();
    }

    private function hasReachedAttemptLimit(Assignment|Exam $assessment): bool
    {
        $limit = $this->attemptLimit($assessment);

        if ($limit === null) {
            return false;
        }

        return $this->submittedAttemptsCount($assessment) >= $limit;
    }

    private function attemptLimit(Assignment|Exam $assessment): ?int
    {
        $limit = $assessment->num_of_attempts;

        if (blank($limit) || (int) $limit <= 0) {
            return null;
        }

        return (int) $limit;
    }

    private function submittedAttemptsCount(Assignment|Exam $assessment): int
    {
        return StudentAttempt::query()
            ->where('student_user_id', Auth::id())
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
    private function questions(Assignment|Exam $assessment): Collection
    {
        $questionIds = $assessment instanceof Exam
            ? $this->examModel($assessment)?->questionIdList()->all() ?? []
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

    private function examModel(Exam $exam): ?ExamModel
    {
        $existingAttempt = $this->latestSubmittedAttempt($exam);

        if ($existingAttempt?->examModel) {
            return $existingAttempt->examModel;
        }

        if ($exam->relationLoaded('models')) {
            return $exam->models->sortBy('model_number')->first();
        }

        return $exam->models()->oldest('model_number')->first();
    }

    private function latestSubmittedAttempt(Assignment|Exam $assessment): ?StudentAttempt
    {
        return StudentAttempt::query()
            ->with(['examModel', 'currentStatus.type'])
            ->where('student_user_id', Auth::id())
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

    private function inProgressAttempt(Assignment|Exam $assessment): ?StudentAttempt
    {
        return StudentAttempt::query()
            ->with(['currentStatus.type'])
            ->where('student_user_id', Auth::id())
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas('currentStatus.type', fn (Builder $query): Builder => $query->where('slug', 'in_progress'))
            ->latest()
            ->first();
    }

    /**
     * @param  Collection<int, Question>  $questions
     */
    private function attempt(Assignment|Exam $assessment, Collection $questions, ?ExamModel $examModel): StudentAttempt
    {
        $inProgressAttempt = StudentAttempt::query()
            ->where('student_user_id', Auth::id())
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->whereHas('currentStatus.type', fn (Builder $query): Builder => $query->where('slug', 'in_progress'))
            ->latest()
            ->first();

        if ($inProgressAttempt) {
            return $inProgressAttempt;
        }

        $attemptNumber = ((int) StudentAttempt::query()
            ->where('student_user_id', Auth::id())
            ->where('attemptable_type', $assessment::class)
            ->where('attemptable_id', $assessment->id)
            ->max('attempt_number')) + 1;

        return StudentAttempt::query()->create([
            'student_user_id' => Auth::id(),
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

    private function storeAnswer(StudentAttempt $attempt, Question $question, ?ExamModel $examModel, int $questionsCount, bool $force = false): void
    {
        $answer = $this->answers[$question->id] ?? null;
        $questionMaxScore = $this->questionMaxScore($attempt, $question, $examModel, $questionsCount);
        $questionOptionId = null;
        $answerText = null;
        $isCorrect = null;
        $score = null;

        if ($question->type === QuestionType::Statement) {
            if ($this->isBlankAnswer($answer)) {
                $answerText = null;
            } else {
                $answerText = trim((string) $answer);
            }

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
     */
    private function finalizeAttempt(Assignment|Exam $assessment, Collection $questions, ?ExamModel $examModel, StudentAttempt $attempt, bool $force): StudentAttempt
    {
        $attempt->studentAnswers()->delete();

        foreach ($questions as $question) {
            $this->storeAnswer($attempt, $question, $examModel, $questions->count(), $force);
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

    private function resultUrl(StudentAttempt $attempt): string
    {
        $path = $this->type === 'exam' ? '/quiz_done' : '/home_work_done';

        return "{$path}?attempt={$attempt->id}";
    }

    private function retryUrl(Assignment|Exam $assessment): string
    {
        $path = $assessment instanceof Exam ? '/quiz' : '/home_work';
        $parameters = $assessment instanceof Exam
            ? ['exam' => $assessment->id]
            : ['assignment' => $assessment->id];

        if ($this->itemId) {
            $parameters['item'] = $this->itemId;
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

    private function isBlankAnswer(mixed $answer): bool
    {
        return $answer === null || (is_string($answer) && trim($answer) === '');
    }
}
