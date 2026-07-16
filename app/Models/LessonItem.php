<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\LessonTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class LessonItem extends Model
{
    use FiltersByTenant, HasTranslations;

    protected $guarded = [];

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
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(Assignment::class, 'lesson_item_assignments')
            ->withPivot(['sort_order'])
            ->withTimestamps();
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'lesson_item_exams')
            ->withPivot(['sort_order'])
            ->withTimestamps();
    }
}
