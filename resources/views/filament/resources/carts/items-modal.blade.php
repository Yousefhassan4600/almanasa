<div style="padding: 0.5rem;">
    <div style="display: grid; gap: 0.75rem;">
        @forelse($items as $item)
            @php
                $purchaseType = $item->purchase_type?->value ?? $item->purchase_type;
                $purchaseTypeLabel = \App\Enums\PurchaseType::options()[$purchaseType] ?? '-';
            @endphp

            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div style="flex: 1;">
                        <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                            {{ __('Item') }} #{{ $loop->iteration }}
                        </p>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #111827;">
                            {{ $item->title ?? $item->course?->title ?? __('Unknown Course') }}
                        </h4>
                    </div>

                    <span style="display: inline-flex; align-items: center; border-radius: 999px; background: #eef2ff; color: #3730a3; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                        {{ $purchaseTypeLabel }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem 1rem; color: #374151; font-size: 0.85rem;">
                    <div>
                        <span style="font-weight: 700;">{{ __('Course') }}:</span>
                        <span>{{ $item->course?->title ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Purchase Unit') }}:</span>
                        <span>{{ $item->purchaseUnit?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Unit Price') }}:</span>
                        <span>{{ number_format((float) $item->unit_price, 2) }}</span>
                    </div>
                    <div>
                        <span style="font-weight: 700;">{{ __('Total') }}:</span>
                        <span>{{ number_format((float) $item->total, 2) }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; color: #64748b; text-align: center;">
                {{ __('No items found for this cart.') }}
            </div>
        @endforelse
    </div>
</div>
