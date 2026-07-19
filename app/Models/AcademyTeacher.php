<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademyTeacher extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'teacher_account_id',
        'image',
        'experience_years',
        'is_active',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'experience_years' => 'integer',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'teacher_account_id');
    }

    public function gradeSubjectAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacherGradeSubject::class);
    }

    public function accountSubjects(): BelongsToMany
    {
        return $this->belongsToMany(AccountSubject::class, 'academy_teacher_grade_subjects')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }
}
