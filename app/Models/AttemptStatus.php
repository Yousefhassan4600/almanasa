<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptStatus extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected array $tenantRelations = [
        'studentAttempt',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'status_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AttemptStatus $attemptStatus): void {
            $attemptStatus->status_at ??= now();
        });

        static::saved(function (AttemptStatus $attemptStatus): void {
            if (! $attemptStatus->is_current) {
                return;
            }

            static::query()
                ->where('student_attempt_id', $attemptStatus->student_attempt_id)
                ->whereKeyNot($attemptStatus->getKey())
                ->update(['is_current' => false]);
        });
    }

    public function studentAttempt(): BelongsTo
    {
        return $this->belongsTo(StudentAttempt::class, 'student_attempt_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AttemptStatusType::class, 'attempt_status_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
