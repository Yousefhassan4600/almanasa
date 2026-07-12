<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyTeacher extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => AccountStatus::class,
            'joined_at' => 'datetime',
        ];
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'academy_account_id');
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
