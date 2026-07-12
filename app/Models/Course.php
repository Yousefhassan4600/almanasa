<?php

namespace App\Models;

use App\Enums\CourseStatus;
use App\Enums\Currency;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'learning_outcomes' => 'array',
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'published_at' => 'datetime',
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'currency' => Currency::class,
            'status' => CourseStatus::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function teacherAssignment(): BelongsTo
    {
        return $this->belongsTo(TeacherGradeSubjectAssignment::class, 'teacher_grade_subject_assignment_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
