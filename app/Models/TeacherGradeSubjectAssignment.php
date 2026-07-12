<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherGradeSubjectAssignment extends Model
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
        return trim(($this->tenantUser?->display_name ?? 'Teacher').' - '.($this->tenantGradeSubject?->display_name ?? 'Grade subject'));
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tenantUser(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class);
    }

    public function tenantGradeSubject(): BelongsTo
    {
        return $this->belongsTo(TenantGradeSubject::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
