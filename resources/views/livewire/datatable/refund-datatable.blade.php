<div>
    <!-- Filters and Search -->
    <div class="mb-6 space-y-4">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="max-w-md flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="lucide:search" class="h-5 w-5 text-gray-400"></iconify-icon>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control w-full pl-10"
                        placeholder="{{ __('Search refunds...') }}"
                    />
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <select wire:model.live="perPage" class="form-control w-20">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                <select wire:model.live="status" class="form-control">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($filters['status'] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date From') }}</label>
                <div wire:ignore>
                    <input
                        type="text"
                        class="form-control"
                        x-data
                        x-init="
                            const fp = flatpickr($el, {
                                enableTime: false,
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'F j, Y',
                                defaultDate: @js($date_from ?: null),
                                onChange: function(selectedDates, dateStr) {
                                    $wire.set('date_from', dateStr, true);
                                }
                            });

                            Livewire.hook('morph.updated', () => {
                                const next = @js($date_from ?: null);
                                if (next && fp.input.value !== next) {
                                    fp.setDate(next, false);
                                }
                            });
                        "
                        autocomplete="off"
                    />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date To') }}</label>
                <div wire:ignore>
                    <input
                        type="text"
                        class="form-control"
                        x-data
                        x-init="
                            const fp = flatpickr($el, {
                                enableTime: false,
                                dateFormat: 'Y-m-d',
                                altInput: true,
                                altFormat: 'F j, Y',
                                defaultDate: @js($date_to ?: null),
                                onChange: function(selectedDates, dateStr) {
                                    $wire.set('date_to', dateStr, true);
                                }
                            });

                            Livewire.hook('morph.updated', () => {
                                const next = @js($date_to ?: null);
                                if (next && fp.input.value !== next) {
                                    fp.setDate(next, false);
                                }
                            });
                        "
                        autocomplete="off"
                    />
                </div>
            </div>

            <div class="flex items-end">
                <button type="button" wire:click="resetFilters" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Refunds Table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th wire:click="sortBy('refund_number')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Refund #') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Order') }}
                        </th>
                        <th wire:click="sortBy('refund_amount')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Amount') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Reason') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Provider Reversed') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Status') }}
                        </th>
                        <th wire:click="sortBy('created_at')" class="cursor-pointer px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Date') }}
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($refunds as $refund)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.refunds.show', $refund) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ $refund->refund_number }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                                {{ class_basename($refund->order_type) }} #{{ $refund->order_id }}
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $refund->formatted_refund_amount }}
                            </td>
                            <td class="max-w-xs truncate px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ Str::limit($refund->reason, 50) }}
                            </td>
                            <td class="px-4 py-4">
                                @if($refund->provider_payment_reversed)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <iconify-icon icon="lucide:check" class="mr-1 h-3 w-3"></iconify-icon>
                                        {{ __('Yes') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <iconify-icon icon="lucide:x" class="mr-1 h-3 w-3"></iconify-icon>
                                        {{ __('No') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $this->getStatusBadgeClass($refund->status) }}">
                                    {{ $refund->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $refund->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <a href="{{ route('admin.refunds.show', $refund) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <iconify-icon icon="lucide:undo-2" class="mx-auto h-12 w-12 text-gray-400"></iconify-icon>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No refunds found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
            {{ $refunds->links() }}
        </div>
    </div>
</div>
