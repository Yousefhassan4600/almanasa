<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PurchaseType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'order_id',
        'course_id',
        'course_price_id',
        'purchase_unit_id',
        'purchase_type',
        'title',
        'unit_price',
        'total',
        'deleted_by',
    ];

    protected $attributes = [
        'purchase_type' => 'single_course',
    ];

    protected array $tenantRelations = [
        'order',
        'course',
    ];

    protected function casts(): array
    {
        return [
            'purchase_type' => PurchaseType::class,
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function coursePrice(): BelongsTo
    {
        return $this->belongsTo(CoursePrice::class, 'course_price_id');
    }

    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(PurchaseUnit::class, 'purchase_unit_id');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'order_item_id');
    }
}
