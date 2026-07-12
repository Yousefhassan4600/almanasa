<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountSubject extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected $appends = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function gradeSubject(): BelongsTo
    {
        return $this->belongsTo(GradeSubject::class, 'grade_subject_id');
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacherGradeSubject::class);
    }

    public function getNameAttribute(): string
    {
        $account = $this->relationLoaded('account') ? $this->account?->name : $this->account()->value('name');
        $gradeSubject = $this->relationLoaded('gradeSubject')
            ? $this->gradeSubject?->name
            : $this->gradeSubject()->with(['grade', 'subject', 'track'])->first()?->name;

        return collect([$account, $gradeSubject])->filter()->join(' - ');
    }
}
