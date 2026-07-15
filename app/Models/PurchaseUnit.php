<?php

namespace App\Models;

use App\Enums\PurchaseUnitType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PurchaseUnit extends Model
{
    use HasTranslations;

    protected $guarded = [];

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
}
