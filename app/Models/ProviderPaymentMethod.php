<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderPaymentMethod extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'provider_id',
        'payment_method_id',
        'account_number',
        'account_holder',
        'phone_number',
        'phone_holder',
        'deleted_by',
    ];

    protected function name(): Attribute
    {
        return Attribute::get(function (): string {
            $methodName = $this->paymentMethod?->name ?? 'Payment Method';
            $identifier = $this->account_number ?: $this->phone_number;

            return $identifier ? "{$methodName} - {$identifier}" : $methodName;
        });
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
