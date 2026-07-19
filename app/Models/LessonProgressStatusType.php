<?php

namespace App\Models;

use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class LessonProgressStatusType extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'sort_order',
        'name',
        'slug',
        'is_active',
        'deleted_by',
    ];

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
