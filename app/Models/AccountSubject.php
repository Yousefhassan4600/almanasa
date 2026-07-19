<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountSubject extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'grade_subject_id',
        'is_active',
        'deleted_by',
    ];

    protected $appends = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function gradeSubject(): BelongsTo
    {
        return $this->belongsTo(GradeSubject::class, 'grade_subject_id');
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacherGradeSubject::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'account_subject_id');
    }

    public function getNameAttribute(): string
    {
        $provider = $this->relationLoaded('provider') ? $this->provider?->name : $this->provider()->value('name');
        $gradeSubject = $this->relationLoaded('gradeSubject')
            ? $this->gradeSubject?->name
            : $this->gradeSubject()->with(['grade', 'subject.track'])->first()?->name;

        return $gradeSubject;
    }
}
