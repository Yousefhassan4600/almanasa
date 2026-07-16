<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class LessonProgressStatusType extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(LessonProgressStatus::class, 'lesson_progress_status_type_id');
    }
}
