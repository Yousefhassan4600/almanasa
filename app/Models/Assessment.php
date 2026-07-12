<?php

namespace App\Models;

use App\Enums\AssessmentType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_explanations' => 'boolean',
            'allow_retry' => 'boolean',
            'is_published' => 'boolean',
            'type' => AssessmentType::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }
}
