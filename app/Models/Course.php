<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use FiltersByTenant, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public array $translatable = [
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'academy_percentage' => 'decimal:2',
            'teacher_percentage' => 'decimal:2',
            'platform_percentage' => 'decimal:2',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function academyTeacher(): BelongsTo
    {
        return $this->belongsTo(AcademyTeacher::class, 'academy_teacher_id');
    }

    public function accountSubject(): BelongsTo
    {
        return $this->belongsTo(AccountSubject::class, 'account_subject_id');
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(CourseOutcome::class, 'course_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CoursePrice::class, 'course_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }
}
