<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    ];

    protected function casts(): array
    {
        return [
            'type' => ProviderType::class,
            'latitude' => 'decimal:2',
            'longitude' => 'decimal:2',
            'pause_website' => 'boolean',
            'is_active' => 'boolean',
            'use_custom_domain' => 'boolean',
        ];
    }

    protected function termsConditions(): Attribute
    {
        return Attribute::get(function (?string $value): ?string {
            if (blank($value)) {
                return $value;
            }

            $decodedValue = json_decode($value, true);

            if (! is_array($decodedValue)) {
                return $value;
            }

            return $decodedValue[app()->getLocale()]
                ?? $decodedValue[config('app.fallback_locale')]
                ?? collect($decodedValue)->first();
        });
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

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
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

    public function providerPaymentMethods(): HasMany
    {
        return $this->hasMany(ProviderPaymentMethod::class);
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
        return ! $this->pause_website && $this->activeSubscription()->exists();
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
