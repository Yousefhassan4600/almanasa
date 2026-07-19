<?php

namespace App\Models;

use App\Enums\CoursePeriodType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CoursePeriod extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'type',
        'name',
        'sort_order',
        'is_active',
        'deleted_by',
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'type' => CoursePeriodType::class,
            'is_active' => 'boolean',
        ];
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'course_period_id');
    }
}
