@php
    $values = $values ?? [];
    $changedKeys = $changedKeys ?? [];

    $formatValue = function (mixed $value): string {
        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) ?: 'null';
    };
@endphp

<div dir="ltr" class="rounded-lg border border-gray-200 bg-gray-50 p-4 font-mono text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    <div>{</div>

    @forelse ($values as $key => $value)
        @php
            $isChanged = in_array($key, $changedKeys, true);
            $formattedValue = $formatValue($value);
            $isLast = $loop->last;
        @endphp

        <div @class([
            'my-1 rounded-md px-2 py-1',
            'bg-amber-100 text-amber-950 ring-1 ring-amber-300 dark:bg-amber-500/15 dark:text-amber-100 dark:ring-amber-400/40' => $isChanged,
        ])>
            <span @class([
                'font-semibold',
                'text-amber-700 dark:text-amber-200' => $isChanged,
                'text-gray-700 dark:text-gray-200' => ! $isChanged,
            ])>"{{ $key }}"</span>: {!! nl2br(e($formattedValue)) !!}{{ $isLast ? '' : ',' }}
        </div>
    @empty
        <div class="px-2 py-1 text-gray-500 dark:text-gray-400"></div>
    @endforelse

    <div>}</div>
</div>
