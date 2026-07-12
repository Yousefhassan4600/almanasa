<?php

namespace App\Models;

use App\Enums\ProgressStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProgress extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'video_progress';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'watch_percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_watched_at' => 'datetime',
            'status' => ProgressStatus::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function lessonContent(): BelongsTo
    {
        return $this->belongsTo(LessonContent::class);
    }
}
