<?php

namespace App\Models;

use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProviderPlan extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'name',
        'description',
        'max_students',
        'max_courses',
        'max_teachers',
        'features',
        'is_active',
        'sort_order',
        'deleted_by',
    ];

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
