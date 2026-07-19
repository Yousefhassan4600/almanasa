<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'account_subject_id',
        'academy_teacher_id',
        'title',
        'description',
        'thumbnail',
        'weekly_lectures_count',
        'num_of_lessons',
        'num_of_hours',
        'academy_percentage',
        'teacher_percentage',
        'platform_percentage',
        'deleted_by',
    ];

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

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, Lesson::class, 'course_id', 'lesson_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'course_id');
    }
}
