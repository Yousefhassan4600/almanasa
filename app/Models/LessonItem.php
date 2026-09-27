<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\LessonTypeEnum;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class LessonItem extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'lesson_id',
        'assignment_id',
        'exam_id',
        'type',
        'title',
        'description',
        'bunny_video_id',
        'video_url',
        'file_url',
        'link_url',
        'duration_seconds',
        'starts_at',
        'ends_at',
        'is_active',
        'is_free',
        'sort_order',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'lesson',
    ];

    public array $translatable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => LessonTypeEnum::class,
            'is_active' => 'boolean',
            'is_free' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'duration_seconds' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LessonItem $lessonItem): void {
            if ($lessonItem->type !== LessonTypeEnum::Assignments) {
                $lessonItem->assignment_id = null;
            }

            if ($lessonItem->type !== LessonTypeEnum::Exams) {
                $lessonItem->exam_id = null;
            }
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function studentVideoProgresses(): HasMany
    {
        return $this->hasMany(StudentVideoProgress::class, 'lesson_item_id');
    }

    public function scopeCurrentlyOpen(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isCurrentlyOpen(): bool
    {
        return $this->is_active;
    }
}
