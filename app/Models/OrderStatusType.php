<?php

namespace App\Models;

use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class OrderStatusType extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
        'deleted_by',
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class, 'order_status_type_id');
    }
}
