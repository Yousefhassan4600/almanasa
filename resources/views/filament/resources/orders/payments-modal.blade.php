<div style="padding: 0.5rem;">
    <div style="display: grid; gap: 0.75rem;">
        @forelse($payments as $payment)
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                            {{ __('admin.labels.Payment') }} #{{ $loop->iteration }}
                        </p>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #111827;">
                            {{ $payment->providerPaymentMethod?->paymentMethod?->name ?? __('admin.labels.Payment Method') }}
                        </h4>
                    </div>

                    <span style="display: inline-flex; align-items: center; border-radius: 999px; background: {{ $payment->is_paid ? '#dcfce7' : '#fef3c7' }}; color: {{ $payment->is_paid ? '#166534' : '#92400e' }}; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                        {{ $payment->is_paid ? __('admin.labels.Paid') : __('admin.labels.Unpaid') }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem 1rem; color: #374151; font-size: 0.85rem;">
                    <div>
                        <span style="font-weight: 700;">{{ __('admin.labels.Reference') }}:</span>
                        <span>{{ $payment->transaction_reference ?? '-' }}</span>
                    </div>
                    @if ($payment->providerCode?->code)
                        <div>
                            <span style="font-weight: 700;">{{ __('admin.labels.Provider Code') }}:</span>
                            <span>{{ $payment->providerCode->code }}</span>
                        </div>
                    @endif

                    @if ($payment->transfer_image)
                        <div style="grid-column: 1 / -1;">
                            <span style="font-weight: 700;">{{ __('admin.labels.Transfer Image') }}:</span>
                            <div style="margin-top: 0.5rem;">
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($payment->transfer_image) }}"
                                    alt="{{ __('admin.labels.Transfer Image') }}"
                                    style="display: block; max-width: 100%; max-height: 280px; border: 1px solid #e5e7eb; border-radius: 8px; object-fit: contain;"
                                >
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; color: #64748b; text-align: center;">
                {{ __('admin.messages.no_payments_found_for_this_order') }}
            </div>
        @endforelse
    </div>
</div>
