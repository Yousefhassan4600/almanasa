<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Assignment extends Model
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
            'lesson_ids' => 'array',
            'question_ids' => 'array',
            'starts_at' => 'datetime',
            'is_today_only' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessonItems(): HasMany
    {
        return $this->hasMany(LessonItem::class, 'assignment_id');
    }

    public function selectedQuestions(): Builder
    {
        $questionIds = $this->question_ids ?? [];

        if ($questionIds === []) {
            return Question::query()->whereRaw('1 = 0');
        }

        return Question::query()->whereIn('id', $questionIds);
    }
}
