<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasTranslations;

    protected $guarded = [];

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
