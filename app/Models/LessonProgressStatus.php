<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgressStatus extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected array $tenantRelations = [
        'lessonProgress',
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
        static::creating(function (LessonProgressStatus $lessonProgressStatus): void {
            $lessonProgressStatus->status_at ??= now();
        });

        static::saved(function (LessonProgressStatus $lessonProgressStatus): void {
            if (! $lessonProgressStatus->is_current) {
                return;
            }

            static::query()
                ->where('lesson_progress_id', $lessonProgressStatus->lesson_progress_id)
                ->whereKeyNot($lessonProgressStatus->getKey())
                ->update(['is_current' => false]);
        });
    }

    public function lessonProgress(): BelongsTo
    {
        return $this->belongsTo(LessonProgress::class, 'lesson_progress_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LessonProgressStatusType::class, 'lesson_progress_status_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
