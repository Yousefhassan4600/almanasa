<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttemptStatus extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'student_attempt_id',
        'attempt_status_type_id',
        'created_by_user_id',
        'is_current',
        'notes',
        'status_at',
        'deleted_by',
    ];

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
