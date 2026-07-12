<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserStatus;
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
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === UserStatus::Active
            && $this->hasDashboardAccount();
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(AccountMembership::class);
    }

    public function activeMemberships(): HasMany
    {
        return $this->memberships()->where('status', 'active');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'account_memberships')
            ->withPivot(['account_id', 'predefined_role', 'status', 'created_by_user_id', 'joined_at'])
            ->withTimestamps();
    }

    public function ownedAccounts(): HasMany
    {
        return $this->hasMany(Account::class, 'owner_user_id');
    }

    public function hasDashboardAccount(): bool
    {
        return $this->activeMemberships()
            ->whereHas('account', fn ($query) => $query
                ->where('status', 'active')
                ->whereIn('type', ['saas_owner', 'academy', 'academy_teacher', 'standalone_teacher']))
            ->exists();
    }

    public function hasWebsiteAccount(): bool
    {
        return $this->activeMemberships()
            ->whereHas('account', fn ($query) => $query
                ->where('status', 'active')
                ->whereIn('type', ['student', 'parent']))
            ->exists();
    }

    public function canUseAccount(Account $account): bool
    {
        return $this->activeMemberships()
            ->where('account_id', $account->id)
            ->exists();
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'gender' => Gender::class,
            'status' => UserStatus::class,
        ];
    }
}
