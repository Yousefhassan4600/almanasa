<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use FiltersByTenant, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'provider_id', 'provider_id');
    }

    public function accountSubjects(): HasMany
    {
        return $this->hasMany(AccountSubject::class, 'provider_id', 'provider_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'provider_id', 'provider_id');
    }

    public function academyTeacherAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'teacher_account_id');
    }

    public function academyTeachers(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'provider_id', 'provider_id');
    }

    public function canAccessDashboard(): bool
    {
        return $this->is_active
            && $this->type->canAccessDashboard();
    }

    public function canAccessWebsite(): bool
    {
        return $this->is_active
            && $this->type->canAccessWebsite();
    }

    public function canCreateSubAccounts(): bool
    {
        return $this->is_active
            && $this->type->canCreateSubAccounts();
    }
}
