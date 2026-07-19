<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PurchaseType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'student_user_id',
        'provider_id',
        'purchase_type',
        'subtotal',
        'total',
        'deleted_by',
    ];

    protected $attributes = [
        'purchase_type' => 'single_course',
        'subtotal' => 0,
        'total' => 0,
    ];

    protected function casts(): array
    {
        return [
            'purchase_type' => PurchaseType::class,
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function purchaseUnit(): HasOneThrough
    {
        return $this->hasOneThrough(
            PurchaseUnit::class,
            CartItem::class,
            'cart_id',
            'id',
            'id',
            'purchase_unit_id',
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cart_id');
    }
}
