<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantGradeSubject extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->tenant?->name ?? 'Tenant').' - '.($this->gradeSubject?->display_name ?? 'Grade subject'));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function gradeSubject(): BelongsTo
    {
        return $this->belongsTo(GradeSubject::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherGradeSubjectAssignment::class);
    }
}
