<?php

namespace App\Models;

use App\Enums\AssessmentAttemptStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAttempt extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'expires_at' => 'datetime',
            'score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_passed' => 'boolean',
            'status' => AssessmentAttemptStatus::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->assessment?->title ?? 'Assessment').' - '.($this->student?->name ?? 'Student').' #'.$this->attempt_number);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
