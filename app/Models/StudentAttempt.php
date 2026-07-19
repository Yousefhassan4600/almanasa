<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class StudentAttempt extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'student_user_id',
        'course_id',
        'exam_model_id',
        'attemptable_type',
        'attemptable_id',
        'attempt_number',
        'max_score',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'course',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StudentAttempt $studentAttempt): void {
            if (! $studentAttempt->isExamAttempt()) {
                return;
            }

            $exists = static::query()
                ->where('student_user_id', $studentAttempt->student_user_id)
                ->where('attemptable_type', Exam::class)
                ->where('attemptable_id', $studentAttempt->attemptable_id)
                ->where('attempt_number', $studentAttempt->attempt_number)
                ->when(
                    $studentAttempt->exists,
                    fn (Builder $query): Builder => $query->whereKeyNot($studentAttempt->getKey())
                )
                ->exists();

            if (! $exists) {
                return;
            }

            throw ValidationException::withMessages([
                'attemptable_id' => 'This student already has this attempt number for this exam.',
            ]);
        });

        static::created(function (StudentAttempt $studentAttempt): void {
            $initialStatusType = AttemptStatusType::query()
                ->where('slug', 'in_progress')
                ->first();

            if (! $initialStatusType) {
                return;
            }

            $studentAttempt->statuses()->create([
                'attempt_status_type_id' => $initialStatusType->id,
                'is_current' => true,
                'status_at' => now(),
            ]);
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function examModel(): BelongsTo
    {
        return $this->belongsTo(ExamModel::class, 'exam_model_id');
    }

    public function attemptable(): MorphTo
    {
        return $this->morphTo();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(AttemptStatus::class, 'student_attempt_id');
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'student_attempt_id');
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(AttemptStatus::class, 'student_attempt_id')
            ->where('is_current', true)
            ->latestOfMany();
    }

    public function isExamAttempt(): bool
    {
        return is_a((string) $this->attemptable_type, Exam::class, true);
    }

    protected function score(): Attribute
    {
        return Attribute::get(function (): float {
            $answers = $this->relationLoaded('studentAnswers')
                ? $this->studentAnswers
                : $this->studentAnswers()->get(['score']);

            return round($answers->sum(fn (StudentAnswer $answer): float => (float) ($answer->score ?? 0)), 2);
        });
    }

    protected function percentage(): Attribute
    {
        return Attribute::get(function (): float {
            $maxScore = (float) ($this->max_score ?? 0);

            if ($maxScore <= 0) {
                return 0;
            }

            return round(($this->score / $maxScore) * 100, 2);
        });
    }

    protected function timeSpentSeconds(): Attribute
    {
        return Attribute::get(function (): ?int {
            $statuses = $this->relationLoaded('statuses')
                ? $this->statuses
                : $this->statuses()->with('type')->oldest('status_at')->get();

            $startedAt = $statuses
                ->first(fn (AttemptStatus $status): bool => $status->type?->slug === 'in_progress')
                ?->status_at;
            $submittedAt = $statuses
                ->first(fn (AttemptStatus $status): bool => $status->type?->slug === 'submitted')
                ?->status_at;

            if (! $startedAt || ! $submittedAt) {
                return null;
            }

            return (int) $startedAt->diffInSeconds($submittedAt);
        });
    }
}
