<?php

namespace App\Models;

use App\Enums\CoursePeriodType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class CoursePeriod extends Model
{
    use HasTranslations;

    protected $guarded = [];

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
