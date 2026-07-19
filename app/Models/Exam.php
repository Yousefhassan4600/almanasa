<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Exam extends Model
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
            'max_degree' => 'decimal:2',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(ExamModel::class, 'exam_id');
    }

    public function lessonItems(): HasMany
    {
        return $this->hasMany(LessonItem::class, 'exam_id');
    }

    public function courseQuestions(): Builder
    {
        if (! $this->course_id || ! $this->models()->exists()) {
            return Question::query()->whereRaw('1 = 0');
        }

        return Question::query()
            ->whereHas('lesson', fn(Builder $query): Builder => $query->where('course_id', $this->course_id));
    }
}
