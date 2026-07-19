<?php

namespace App\Models;

use App\Concerns\FiltersByTenant;
use App\Enums\PaymentMethodSlugs;
use App\Models\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use FiltersByTenant;
    use SoftDeletes, SoftDeletesWithUser;

    protected $fillable = [
        'order_id',
        'provider_id',
        'student_user_id',
        'provider_payment_method_id',
        'transaction_reference',
        'provider_code_id',
        'transfer_image',
        'is_paid',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Payment $payment): void {
            if (! $payment->provider_code_id) {
                return;
            }

            $payment->loadMissing('order');

            $providerCode = ProviderCode::query()->findOrFail($payment->provider_code_id);
            $providerCode->assertValidForPayment($payment);

            $payment->is_paid = true;
            $payment->provider_payment_method_id ??= ProviderPaymentMethod::query()
                ->where('provider_id', $payment->provider_id)
                ->whereHas(
                    'paymentMethod',
                    fn ($query) => $query
                        ->where('slug', PaymentMethodSlugs::Code->value)
                        ->where('is_active', true)
                )
                ->value('id');
        });

        static::saved(function (Payment $payment): void {
            if (! $payment->is_paid || (! $payment->wasRecentlyCreated && ! $payment->wasChanged('is_paid'))) {
                return;
            }

            DB::transaction(function () use ($payment): void {
                $payment->order?->markAsPaid();
                $payment->order?->createMissingSubscriptionsForItems();
            });
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function providerPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(ProviderPaymentMethod::class, 'provider_payment_method_id');
    }

    public function providerCode(): BelongsTo
    {
        return $this->belongsTo(ProviderCode::class, 'provider_code_id');
    }
}
