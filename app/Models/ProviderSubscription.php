<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\ProviderSubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderSubscription extends Model
{
    use FiltersByTenant;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ProviderSubscriptionStatus::class,
            'amount' => 'decimal:2',
            'trial_ends_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProviderPlan::class, 'provider_plan_id');
    }

    public function scopeActiveForAccess(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [
                ProviderSubscriptionStatus::Trialing->value,
                ProviderSubscriptionStatus::Active->value,
            ])
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('trial_ends_at')
                    ->orWhere('trial_ends_at', '>=', now());
            });
    }
}
