<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonProgress extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'student_user_id',
        'course_id',
        'lesson_id',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'course',
    ];

    protected static function booted(): void
    {
        static::created(function (LessonProgress $lessonProgress): void {
            $initialStatusType = LessonProgressStatusType::query()
                ->where('slug', 'not_started')
                ->first();

            if (! $initialStatusType) {
                return;
            }

            $lessonProgress->statuses()->create([
                'lesson_progress_status_type_id' => $initialStatusType->id,
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

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(LessonProgressStatus::class, 'lesson_progress_id');
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(LessonProgressStatus::class, 'lesson_progress_id')
            ->where('is_current', true)
            ->latestOfMany();
    }
}
