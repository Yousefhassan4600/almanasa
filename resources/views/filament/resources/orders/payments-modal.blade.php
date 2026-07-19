<div style="padding: 0.5rem;">
    <div style="display: grid; gap: 0.75rem;">
        @forelse($payments as $payment)
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                            {{ __('Payment') }} #{{ $loop->iteration }}
                        </p>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #111827;">
                            {{ $payment->providerPaymentMethod?->paymentMethod?->name ?? __('Payment Method') }}
                        </h4>
                    </div>

                    <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #dcfce7; color: #166534; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                        {{ number_format((float) $payment->amount, 2) }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem 1rem; color: #374151; font-size: 0.85rem;">
                    <div>
                        <span style="font-weight: 700;">{{ __('Reference') }}:</span>
                        <span>{{ $payment->transaction_reference ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Provider Code') }}:</span>
                        <span>{{ $payment->providerCode?->code ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Sender Phone') }}:</span>
                        <span>{{ $payment->sender_phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Paid At') }}:</span>
                        <span>{{ $payment->paid_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Reviewed By') }}:</span>
                        <span>{{ $payment->reviewedBy?->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Reviewed At') }}:</span>
                        <span>{{ $payment->reviewed_at?->format('Y-m-d H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; color: #64748b; text-align: center;">
                {{ __('No payments found for this order.') }}
            </div>
        @endforelse
    </div>
</div>
