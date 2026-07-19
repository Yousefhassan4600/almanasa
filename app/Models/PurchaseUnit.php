<?php

namespace App\Models;

use App\Enums\PurchaseUnitType;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PurchaseUnit extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'type',
        'name',
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
            'type' => PurchaseUnitType::class,
            'is_active' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CoursePrice::class, 'purchase_unit_id');
    }

    public function providerCodes(): HasMany
    {
        return $this->hasMany(ProviderCode::class, 'purchase_unit_id');
    }
}
