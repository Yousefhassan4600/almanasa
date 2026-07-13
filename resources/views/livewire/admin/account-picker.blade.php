<div>
    @if ($accounts->count() > 1)
        <div class="fi-admin-account-picker mx-3 hidden min-w-72 sm:block">
            <div class="mb-1 px-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                {{-- Current account --}}
            </div>
            <x-filament::input.wrapper>
                <x-filament::input.select
                    class="min-h-10 px-3 py-2 text-sm"
                    wire:change="switchAccount($event.target.value)"
                    wire:loading.attr="disabled"
                    aria-label="Current account"
                >
                    @foreach ($accounts as $account)
                        <option
                            value="{{ $account->id }}"
                            @selected($selectedAccountId === (string) $account->id)
                        >
                            {{ $this->accountLabel($account) }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    @elseif ($accounts->count() === 1)
        <div class="fi-admin-account-picker mx-3 hidden min-w-72 sm:block">
            {{-- <div class="mb-1 px-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                Current account
            </div> --}}
            <div class="flex min-h-10 items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                {{ $this->accountLabel($accounts->first()) }}
            </div>
        </div>
    @endif
</div>
