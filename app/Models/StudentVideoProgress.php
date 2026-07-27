<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentVideoProgress extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $table = 'student_video_progress';

    protected $fillable = [
        'student_user_id',
        'course_id',
        'lesson_id',
        'lesson_item_id',
        'watch_number',
        'duration_seconds',
        'watched_seconds',
        'last_position_seconds',
        'progress_percentage',
        'completed_at',
        'last_watched_at',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'course',
    ];

    protected function casts(): array
    {
        return [
            'watch_number' => 'integer',
            'duration_seconds' => 'integer',
            'watched_seconds' => 'integer',
            'last_position_seconds' => 'integer',
            'progress_percentage' => 'integer',
            'completed_at' => 'datetime',
            'last_watched_at' => 'datetime',
        ];
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

    public function lessonItem(): BelongsTo
    {
        return $this->belongsTo(LessonItem::class, 'lesson_item_id');
    }
}
