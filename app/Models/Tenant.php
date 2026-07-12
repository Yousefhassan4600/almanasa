<?php

namespace App\Models;

use App\Enums\TenantStatus;
use App\Enums\TenantType;
use App\Models\Concerns\ScopesTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, ScopesTenantModel;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'status' => TenantStatus::class,
            'type' => TenantType::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function gradeSubjects(): HasMany
    {
        return $this->hasMany(TenantGradeSubject::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherGradeSubjectAssignment::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
