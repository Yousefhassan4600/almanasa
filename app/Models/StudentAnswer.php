<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected array $tenantRelations = [
        'student_attempt',
        'question',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'score' => 'decimal:2',
        ];
    }

    public function student_attempt(): BelongsTo
    {
        return $this->belongsTo(StudentAttempt::class, 'student_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function question_option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }

    protected function correctAnswer(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->requires_manual_grading) {
                return null;
            }

            $question = $this->relationLoaded('question')
                ? $this->question
                : $this->question()->with('options')->first();

            if (! $question) {
                return null;
            }

            if (! $question->relationLoaded('options')) {
                $question->load('options');
            }

            return $question->options
                ->firstWhere('is_correct', true)
                ?->title;
        });
    }

    protected function requiresManualGrading(): Attribute
    {
        return Attribute::get(function (): bool {
            $question = $this->relationLoaded('question')
                ? $this->question
                : $this->question()->first();

            return $question?->type === QuestionType::Statement;
        });
    }

    protected function questionMaxDegree(): Attribute
    {
        return Attribute::get(function (): ?float {
            $attempt = $this->relationLoaded('student_attempt')
                ? $this->student_attempt
                : $this->student_attempt()->first();

            if (! $attempt || $attempt->max_score === null) {
                return null;
            }

            if ($attempt->attemptable_type === Exam::class) {
                $examModel = $attempt->relationLoaded('examModel')
                    ? $attempt->examModel
                    : $attempt->examModel()->first();

                $maxScore = $examModel?->questionMaxScore((int) $this->question_id);

                if ($maxScore !== null) {
                    return round((float) $maxScore, 2);
                }
            }

            $answersCount = $attempt->relationLoaded('studentAnswers')
                ? $attempt->studentAnswers->count()
                : $attempt->studentAnswers()->count();

            if ($answersCount === 0) {
                return null;
            }

            return round((float) $attempt->max_score / $answersCount, 2);
        });
    }
}
