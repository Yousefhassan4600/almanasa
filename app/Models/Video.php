<?php

namespace App\Models;

use App\Enums\VideoProcessingStatus;
use App\Enums\VideoProvider;
use App\Enums\VideoVisibility;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'provider' => VideoProvider::class,
            'processing_status' => VideoProcessingStatus::class,
            'visibility' => VideoVisibility::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->lessonContent?->title ?? $this->provider_video_id ?? "Video #{$this->getKey()}";
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lessonContent(): BelongsTo
    {
        return $this->belongsTo(LessonContent::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }
}
