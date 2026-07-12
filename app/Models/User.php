<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\MembershipStatus;
use App\Models\Concerns\ScopesTenantUsers;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, ScopesTenantUsers;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function ownedTenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_user_id');
    }

    public function tenantMemberships(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    /**
     * @return list<int>
     */
    public function accessibleTenantIds(): array
    {
        $memberTenantIds = TenantUser::query()
            ->withoutGlobalScopes()
            ->where('user_id', $this->id)
            ->where('status', MembershipStatus::Active->value)
            ->pluck('tenant_id');

        $ownedTenantIds = Tenant::query()
            ->withoutGlobalScopes()
            ->where('owner_user_id', $this->id)
            ->pluck('id');

        return $memberTenantIds
            ->merge($ownedTenantIds)
            ->unique()
            ->values()
            ->map(fn (mixed $tenantId): int => (int) $tenantId)
            ->all();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'student_id');
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class, 'student_id');
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'student_id');
    }

    public function videoProgress(): HasMany
    {
        return $this->hasMany(VideoProgress::class, 'student_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
