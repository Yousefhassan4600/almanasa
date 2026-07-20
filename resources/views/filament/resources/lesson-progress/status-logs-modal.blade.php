<div style="padding: 0.5rem;">
    <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border: 1px solid #7dd3fc; padding: 1.5rem; border-radius: 12px;">
        <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
            <div style="width: 32px; height: 32px; background: #0ea5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-inline-end: 0.75rem;">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 style="font-weight: 700; font-size: 1.1rem; color: #075985;">{{ __('admin.labels.Lesson Progress Status History') }}</h3>
        </div>

        <div style="position: relative;">
            @forelse($statusLogs as $statusLog)
                @php
                    $statusDate = $statusLog->status_at ?? $statusLog->created_at;
                @endphp

                <div style="display: flex; margin-bottom: {{ $loop->last ? '0' : '1.5rem' }};">
                    <div style="display: flex; flex-direction: column; align-items: center; margin-inline-end: 1rem;">
                        <div style="width: 12px; height: 12px; background: {{ $loop->first ? '#10b981' : '#6b7280' }}; border-radius: 50%; border: 3px solid {{ $loop->first ? '#d1fae5' : '#e5e7eb' }}; z-index: 10;"></div>
                        @if(! $loop->last)
                            <div style="width: 2px; height: 100%; background: #cbd5e1; flex: 1; min-height: 40px;"></div>
                        @endif
                    </div>

                    <div style="flex: 1; background: rgba(255,255,255,0.7); border-radius: 8px; padding: 1rem; border: 1px solid {{ $loop->first ? '#86efac' : '#cbd5e1' }}; margin-bottom: {{ $loop->last ? '0' : '0.5rem' }};">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem; margin-bottom: 0.5rem;">
                            <div>
                                <h4 style="font-weight: 600; font-size: 0.95rem; color: #0c4a6e; margin-bottom: 0.25rem;">
                                    {{ $statusLog->type?->name ?? __('admin.labels.Unknown Status') }}
                                </h4>
                                @if($loop->first)
                                    <span style="display: inline-block; background: #10b981; color: white; font-size: 0.7rem; font-weight: 600; padding: 0.15rem 0.5rem; border-radius: 4px;">
                                        {{ __('admin.labels.Current Status') }}
                                    </span>
                                @endif
                            </div>
                            <div style="text-align: end;">
                                <p style="font-size: 0.75rem; font-weight: 600; color: #0369a1; margin-bottom: 0.15rem;">
                                    {{ $statusDate?->format('d-m-Y') ?? '-' }}
                                </p>
                                <p style="font-size: 0.7rem; color: #64748b; font-family: monospace;">
                                    {{ $statusDate?->format('g:i a') ?? '-' }}
                                </p>
                            </div>
                        </div>

                        @if($statusLog->createdBy)
                            <p style="font-size: 0.75rem; color: #475569; margin-top: 0.5rem;">
                                {{ __('admin.labels.Changed By') }}: {{ $statusLog->createdBy->name }}
                            </p>
                        @endif

                        @if($statusLog->notes)
                            <p style="font-size: 0.8rem; color: #475569; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid #e2e8f0;">
                                {{ $statusLog->notes }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div style="background: rgba(255,255,255,0.7); border-radius: 8px; padding: 1rem; border: 1px solid #cbd5e1; color: #64748b; text-align: center;">
                    {{ __('admin.messages.no_status_logs_found_for_this_lesson_progress') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
