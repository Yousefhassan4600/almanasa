<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\LessonTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class LessonItem extends Model
{
    use FiltersByTenant, HasTranslations;

    protected $guarded = [];

    protected array $tenantRelations = [
        'lesson',
        'assignment',
        'exam',
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
