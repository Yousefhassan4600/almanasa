<?php

namespace App\Models;

use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderPlanOption extends Model
{
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_plan_id',
        'billing_period_days',
        'price',
        'sort_order',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProviderPlan::class, 'provider_plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProviderSubscription::class);
    }
}
