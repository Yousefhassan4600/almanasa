<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Account extends Model
{
    use HasTranslations, SoftDeletes;

    protected $guarded = [];

    public array $translatable = [
        'bio',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'latitude' => 'decimal:2',
            'longitude' => 'decimal:2',
            'status' => AccountStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(AccountMembership::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function accountSubjects(): HasMany
    {
        return $this->hasMany(AccountSubject::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function academyTeacherAssignments(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'teacher_account_id');
    }

    public function academyTeachers(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class, 'academy_account_id');
    }

    public function canAccessDashboard(): bool
    {
        return $this->status === AccountStatus::Active
            && $this->type->canAccessDashboard();
    }

    public function canAccessWebsite(): bool
    {
        return $this->status === AccountStatus::Active
            && $this->type->canAccessWebsite();
    }

    public function canCreateSubAccounts(): bool
    {
        return $this->status === AccountStatus::Active
            && $this->type->canCreateSubAccounts();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
