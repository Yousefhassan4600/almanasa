<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\CoursePeriodType;
use App\Enums\ProviderType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Provider extends Model
{
    use FiltersByTenant, HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'type',
        'owner_user_id',
        'subject_id',
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'logo',
        'cover_image',
        'bio',
        'country_id',
        'city_id',
        'address',
        'contact_phone',
        'contact_whatsapp',
        'contact_email',
        'youtube_link',
        'facebook_link',
        'instagram_link',
        'linkedin_link',
        'x_link',
        'snapchat_link',
        'terms_conditions',
        'latitude',
        'longitude',
        'primary_color',
        'secondary_color',
        'pause_website',
        'current_course_period_type',
        'completion_watch_percentage',
        'is_active',
        'use_custom_domain',
        'deleted_by',
    ];

    public array $translatable = [
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProviderType::class,
            'current_course_period_type' => CoursePeriodType::class,
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

    protected function websiteUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $domain = $this->use_custom_domain && filled($this->custom_domain)
                ? $this->custom_domain
                : collect([$this->subdomain, config('almanasa.root_domain')])
                    ->filter()
                    ->join('.');

            if (str_starts_with((string) $domain, 'http://') || str_starts_with((string) $domain, 'https://')) {
                return rtrim((string) $domain, '/').'/';
            }

            $appUrl = config('app.url');
            $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';
            $port = parse_url($appUrl, PHP_URL_PORT);
            $port = $port ? ":{$port}" : '';

            return "{$scheme}://".trim((string) $domain, '/').$port.'/';
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
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

    public function providerCodes(): HasMany
    {
        return $this->hasMany(ProviderCode::class);
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
