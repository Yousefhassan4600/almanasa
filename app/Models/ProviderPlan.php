<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;

class ProviderPlan extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProviderPlanOption::class)->orderBy('sort_order');
    }

    public function subscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProviderSubscription::class,
            ProviderPlanOption::class,
            'provider_plan_id',
            'provider_plan_option_id',
        );
    }
}
