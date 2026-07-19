<?php

namespace App\Models;

use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasTranslations;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'sort_order',
        'name',
        'slug',
        'image',
        'is_active',
        'is_bank',
        'require_proof',
        'is_code',
        'deleted_by',
    ];

    protected $attributes = [
        'sort_order' => 0,
        'is_active' => true,
        'is_bank' => false,
        'require_proof' => false,
        'is_code' => false,
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_bank' => 'boolean',
            'require_proof' => 'boolean',
            'is_code' => 'boolean',
        ];
    }

    public function providerPaymentMethods(): HasMany
    {
        return $this->hasMany(ProviderPaymentMethod::class);
    }
}
