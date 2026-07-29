<?php

namespace App\Actions\StudentPortal\Checkout;

use App\Models\Provider;
use App\Models\ProviderPaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class ListProviderPaymentMethods
{
    /**
     * @return Collection<int, ProviderPaymentMethod>
     */
    public function handle(Provider $provider): Collection
    {
        return ProviderPaymentMethod::query()
            ->with('paymentMethod:id,name,slug,image,is_active,is_bank,require_proof,is_code,sort_order')
            ->whereBelongsTo($provider)
            ->whereHas('paymentMethod', fn ($query) => $query->where('is_active', true))
            ->join('payment_methods', 'payment_methods.id', '=', 'provider_payment_methods.payment_method_id')
            ->orderBy('payment_methods.sort_order')
            ->orderBy('provider_payment_methods.id')
            ->select('provider_payment_methods.*')
            ->get();
    }
}
