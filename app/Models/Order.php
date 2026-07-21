<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PurchaseType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'student_user_id',
        'cart_id',
        'order_number',
        'purchase_type',
        'subtotal',
        'total',
        'deleted_by',
    ];

    protected $attributes = [
        'purchase_type' => 'single_course',
    ];

    protected function casts(): array
    {
        return [
            'purchase_type' => PurchaseType::class,
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Order $order): void {
            if (! $order->cart_id) {
                return;
            }

            $cart = Cart::query()
                ->withTrashed()
                ->find($order->cart_id);

            if ($cart && ! $cart->trashed()) {
                $cart->delete();
            }
        });
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id')->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id')->latestOfMany();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class, 'order_id');
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'order_id')
            ->where('is_current', true)
            ->latestOfMany();
    }

    public function purchaseUnit(): HasOneThrough
    {
        return $this->hasOneThrough(
            PurchaseUnit::class,
            OrderItem::class,
            'order_id',
            'id',
            'id',
            'purchase_unit_id',
        );
    }

    public function createMissingSubscriptionsForItems(): void
    {
        $this->loadMissing('items.subscription', 'items.purchaseUnit');

        $this->items
            ->filter(fn (OrderItem $orderItem): bool => ! $orderItem->subscription)
            ->each(function (OrderItem $orderItem): void {
                $startsAt = now();
                $periodDays = $orderItem->purchaseUnit?->period_days;

                $orderItem->subscription()->create([
                    'student_user_id' => $this->student_user_id,
                    'provider_id' => $this->provider_id,
                    'course_id' => $orderItem->course_id,
                    'purchase_unit_id' => $orderItem->purchase_unit_id,
                    'purchase_type' => $orderItem->purchase_type,
                    'starts_at' => $startsAt,
                    'ends_at' => $periodDays ? $startsAt->copy()->addDays($periodDays) : null,
                ]);
            });
    }

    public function markAsPaid(?int $createdByUserId = null): void
    {
        $this->loadMissing('currentStatus.type');

        if ($this->currentStatus?->type?->slug === 'paid') {
            return;
        }

        $paidStatusType = OrderStatusType::query()
            ->where('slug', 'paid')
            ->where('is_active', true)
            ->first();

        if (! $paidStatusType) {
            return;
        }

        $this->statuses()->create([
            'order_status_type_id' => $paidStatusType->id,
            'is_current' => true,
            'status_at' => now(),
            'created_by_user_id' => $createdByUserId,
        ]);
    }
}
