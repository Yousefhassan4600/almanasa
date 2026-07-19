<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'order_id',
        'order_status_type_id',
        'is_current',
        'status_at',
        'notes',
        'created_by_user_id',
        'deleted_by',
    ];

    protected array $tenantRelations = [
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'status_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OrderStatus $orderStatus): void {
            $orderStatus->status_at ??= now();
        });

        static::saved(function (OrderStatus $orderStatus): void {
            if (! $orderStatus->is_current) {
                return;
            }

            static::query()
                ->where('order_id', $orderStatus->order_id)
                ->whereKeyNot($orderStatus->getKey())
                ->update(['is_current' => false]);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrderStatusType::class, 'order_status_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
