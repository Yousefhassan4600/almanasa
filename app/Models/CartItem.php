<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PurchaseType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'cart_id',
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
        'cart',
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

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
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
}
