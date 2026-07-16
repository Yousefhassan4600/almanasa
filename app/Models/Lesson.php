<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\CoursePeriodType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Lesson extends Model
{
    use FiltersByTenant, HasTranslations;

    protected $guarded = [];

    protected array $tenantRelations = [
        'course',
    ];

    public array $translatable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'num_of_video_views' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function coursePeriod(): BelongsTo
    {
        return $this->belongsTo(CoursePeriod::class, 'course_period_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LessonItem::class, 'lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'lesson_id');
    }

    public function scopeVisibleForProviderCurrentPeriod(Builder $query, Provider $provider): Builder
    {
        $periodType = $provider->current_course_period_type instanceof CoursePeriodType
            ? $provider->current_course_period_type
            : CoursePeriodType::Term1;

        return $query->whereHas(
            'coursePeriod',
            fn (Builder $query): Builder => $query->whereIn('type', $periodType->visiblePeriodTypes())
        );
    }
}
