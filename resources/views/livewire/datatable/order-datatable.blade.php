<div>
    <!-- Filters and Search -->
    <div class="mb-6 space-y-4">
        <!-- Search and Quick Filters -->
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <!-- Search -->
            <div class="max-w-md flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="lucide:search" class="h-5 w-5 text-gray-400"></iconify-icon>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control w-full pl-10"
                        placeholder="{{ __('Search orders by number, customer, phone...') }}"
                    />
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">
                <!-- Per Page -->
                <select wire:model.live="perPage" class="form-control w-20">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>

                <!-- Filters Toggle -->
                <button
                    type="button"
                    wire:click="$toggle('showFilters')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <iconify-icon icon="lucide:filter" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Filters') }}
                </button>
            </div>
        </div>

        <!-- Advanced Filters -->
        @if($showFilters)
            <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Status Filter -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    <select wire:model.live="filters.status" class="form-control">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="processing">{{ __('Processing') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                        <option value="failed">{{ __('Failed') }}</option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Payment Status') }}</label>
                    <select wire:model.live="filters.payment_status" class="form-control">
                        <option value="">{{ __('All Payment Statuses') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="processing">{{ __('Processing') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="failed">{{ __('Failed') }}</option>
                        <option value="refunded">{{ __('Refunded') }}</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date From') }}</label>
                    <input type="date" wire:model.live="filters.date_from" class="form-control" />
                </div>

                <!-- Date To -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date To') }}</label>
                    <input type="date" wire:model.live="filters.date_to" class="form-control" />
                </div>

                <!-- Clear Filters -->
                <div class="sm:col-span-2 lg:col-span-4">
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        {{ __('Clear Filters') }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Orders Table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <!-- Order Number -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Order #') }}
                        </th>

                        <!-- Customer -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Customer') }}
                        </th>

                        <!-- Agent -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Agent') }}
                        </th>

                        <!-- Items -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Items') }}
                        </th>

                        <!-- Total -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Total') }}
                        </th>

                        <!-- Payment Status -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Payment') }}
                        </th>

                        <!-- Status -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Status') }}
                        </th>

                        <!-- Date -->
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Date') }}
                        </th>

                        <!-- Actions -->
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($items as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <!-- Order Number -->
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $order->order_number }}
                                </a>
                            </td>

                            <!-- Customer -->
                            <td class="px-4 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer->name }}</p>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $order->customer->phone }}</p>
                                </div>
                            </td>

                            <!-- Agent -->
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                                        <span class="text-xs font-medium">{{ strtoupper(substr($order->agent->first_name ?? 'U', 0, 1) . substr($order->agent->last_name ?? '', 0, 1)) }}</span>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $order->agent->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <!-- Items -->
                            <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $order->items->count() }} {{ __('items') }}
                            </td>

                            <!-- Total -->
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($order->total, 2) }}
                            </td>

                            <!-- Payment Status -->
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $order->payment_status_color }}">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View -->
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-300"
                                       title="{{ __('View Order') }}">
                                        <iconify-icon icon="lucide:eye" class="h-4 w-4"></iconify-icon>
                                    </a>
                                    
                                    @php
                                        $activeRefund = $order->refunds->first(fn ($refund) => $refund->status !== 'rejected');
                                    @endphp

                                    @if($activeRefund)
                                        <a href="{{ route('admin.refunds.show', $activeRefund) }}" 
                                           class="inline-flex items-center justify-center rounded-lg p-2 text-orange-600 hover:bg-orange-50 hover:text-orange-800 dark:text-orange-400 dark:hover:bg-orange-900/20 dark:hover:text-orange-300"
                                           title="{{ __('View Refund') }}">
                                            <iconify-icon icon="lucide:receipt-text" class="h-4 w-4"></iconify-icon>
                                        </a>
                                    @elseif($order->canBeRefunded())
                                        <a href="{{ route('admin.refunds.create', ['order_type' => get_class($order), 'order_id' => $order->id]) }}" 
                                           class="inline-flex items-center justify-center rounded-lg p-2 text-orange-600 hover:bg-orange-50 hover:text-orange-800 dark:text-orange-400 dark:hover:bg-orange-900/20 dark:hover:text-orange-300"
                                           title="{{ __('Initiate Refund') }}">
                                            <iconify-icon icon="lucide:rotate-ccw" class="h-4 w-4"></iconify-icon>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <iconify-icon icon="lucide:package-x" class="mx-auto h-12 w-12 text-gray-400"></iconify-icon>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('No orders found') }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
            {{ $items->links() }}
        </div>
    </div>
</div>
