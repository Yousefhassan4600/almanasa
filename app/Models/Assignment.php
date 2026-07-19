<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Assignment extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'num_of_questions',
        'num_of_easy_questions',
        'num_of_medium_questions',
        'num_of_hard_questions',
        'duration_minutes',
        'num_of_attempts',
        'lesson_ids',
        'question_ids',
        'deleted_by',
    ];

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
