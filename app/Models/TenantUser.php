<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use App\Enums\TenantRole;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantUser extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'role' => TenantRole::class,
            'status' => MembershipStatus::class,
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        $role = $this->role?->label() ?? $this->role?->value ?? 'Member';

        return trim(($this->user?->name ?? 'User').' - '.$role);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherGradeSubjectAssignment::class);
    }
}
