<?php

namespace App\Models;

use App\Enums\PublishingStatus;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'default_score' => 'decimal:2',
            'difficulty' => QuestionDifficulty::class,
            'status' => PublishingStatus::class,
            'type' => QuestionType::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return str($this->body ?? 'Question')->stripTags()->squish()->limit(80)->toString();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }
}
