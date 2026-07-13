<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use FiltersByTenant;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected array $tenantRelations = [
        'employees',
        'ownedAccounts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active
            && $this->hasDashboardAccount();
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function activeEmployees(): HasMany
    {
        return $this->employees()->where('is_active', true);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'employees')
            ->withPivot(['account_id', 'predefined_role', 'is_active', 'created_by_user_id'])
            ->withTimestamps();
    }

    public function ownedAccounts(): HasMany
    {
        return $this->hasMany(Account::class, 'owner_user_id');
    }

    public function hasDashboardAccount(): bool
    {
        $dashboardAccountConstraint = fn ($query) => $query
            ->where('is_active', true)
            ->whereIn('type', ['saas_owner', 'academy', 'academy_teacher', 'standalone_teacher'])
            ->where(function ($query): void {
                $query
                    ->where('type', 'saas_owner')
                    ->orWhereHas('provider.activeSubscription');
            });

        return $this->ownedAccounts()
            ->where($dashboardAccountConstraint)
            ->exists()
            || $this->activeEmployees()
                ->whereHas('account', fn ($query) => $query
                    ->where($dashboardAccountConstraint))
                ->exists();
    }

    public function hasWebsiteAccount(): bool
    {
        return $this->ownedAccounts()
            ->where('is_active', true)
            ->whereIn('type', ['student', 'parent'])
            ->exists();
    }

    public function canUseAccount(Account $account): bool
    {
        if ($account->owner_user_id === $this->id) {
            return true;
        }

        return $this->activeEmployees()
            ->where('account_id', $account->id)
            ->exists();
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
