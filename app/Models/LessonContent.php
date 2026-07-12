<?php

namespace App\Models;

use App\Enums\LessonContentType;
use App\Models\Concerns\ScopedByTenantParent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LessonContent extends Model
{
    use HasFactory, ScopedByTenantParent;

    protected $guarded = [];

    protected static function tenantParentRelation(): string
    {
        return 'lesson';
    }

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_preview' => 'boolean',
            'available_at' => 'datetime',
            'type' => LessonContentType::class,
        ];
    }

    public function getContentableDisplayNameAttribute(): string
    {
        $contentable = $this->contentable;

        return $contentable?->title ?? $contentable?->name ?? $contentable?->display_name ?? class_basename((string) $this->contentable_type).' #'.$this->contentable_id;
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function videoProgress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }
}
