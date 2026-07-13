<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Provider extends Model
{
    use FiltersByTenant, HasTranslations, SoftDeletes;

    protected $guarded = [];

    public array $translatable = [
        'bio',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProviderType::class,
            'latitude' => 'decimal:2',
            'longitude' => 'decimal:2',
            'website_enabled' => 'boolean',
            'registration_enabled' => 'boolean',
            'chat_enabled' => 'boolean',
            'payment_enabled' => 'boolean',
            'tax_percentage' => 'decimal:2',
            'is_active' => 'boolean',
            'use_custom_domain' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
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

    public function academyTeachers(): HasMany
    {
        return $this->hasMany(AcademyTeacher::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProviderSubscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(ProviderSubscription::class)->latestOfMany();
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ProviderSubscription::class)->activeForAccess()->latestOfMany();
    }

    public function canAccessWebsite(): bool
    {
        return $this->activeSubscription()->exists();
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
