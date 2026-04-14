<div class="space-y-6">
    <!-- Provider Search Section -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Select Provider') }}</h3>
        
        <!-- Provider Dropdown -->
        <div>
            <select wire:model.live="selectedProviderId" class="form-control w-full">
                <option value="">{{ __('Select a provider') }}</option>
                @foreach($providers as $provider)
                    <option value="{{ $provider['id'] }}">{{ $provider['name'] }} ({{ $provider['phone'] ?? 'N/A' }})</option>
                @endforeach
            </select>
        </div>
        
        @if (!$selectedProviderId)
            <div class="mt-8 text-center text-gray-500 dark:text-gray-400">
                <iconify-icon icon="lucide:user-search" class="mx-auto mb-2 h-12 w-12 text-gray-300 dark:text-gray-600"></iconify-icon>
                <p>{{ __('Select a provider to view payout details') }}</p>
            </div>
        @else
            <!-- Selected Provider Display -->
            <div class="mt-4 flex items-center rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50">
                    <iconify-icon icon="lucide:building-2" class="h-6 w-6 text-blue-600 dark:text-blue-400"></iconify-icon>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedProvider['name'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $selectedProvider['id'] }} | {{ $selectedProvider['phone'] }}</p>
                </div>
            </div>
        @endif
    </div>

    @if ($selectedProviderId)
        <!-- Date Range Filter -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-wrap items-end gap-4">
                <div class="w-48">
                    <x-inputs.date-picker 
                        name="startDate" 
                        label="{{ __('Start Date') }}" 
                        :value="$startDate"
                        wire:model.live="startDate"
                    />
                </div>
                <div class="w-48">
                    <x-inputs.date-picker 
                        name="endDate" 
                        label="{{ __('End Date') }}" 
                        :value="$endDate"
                        wire:model.live="endDate"
                    />
                </div>
            </div>
        </div>

        <!-- Provider Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Total Earned -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <iconify-icon icon="lucide:wallet" class="h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Earned') }}</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($summary['total_earned'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <iconify-icon icon="lucide:check-circle" class="h-5 w-5 text-green-600 dark:text-green-400"></iconify-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Paid') }}</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($summary['total_paid'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Reversed -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <iconify-icon icon="lucide:rotate-ccw" class="h-5 w-5 text-red-600 dark:text-red-400"></iconify-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Reversed') }}</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">${{ number_format($summary['total_reversed'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Outstanding Balance -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <iconify-icon icon="lucide:clock" class="h-5 w-5 text-yellow-600 dark:text-yellow-400"></iconify-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Outstanding') }}</p>
                        <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($summary['outstanding_balance'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pay Provider Button -->
            <div class="flex items-center justify-center rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @php
                    $hasBalance = ($summary['outstanding_balance'] ?? 0) > 0;
                @endphp
                <button
                    type="button"
                    wire:click="openPaymentModal"
                    {{ $hasBalance ? '' : 'disabled' }}
                    class="btn btn-success w-full {{ !$hasBalance ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    <iconify-icon icon="lucide:banknote" class="mr-2 h-5 w-5"></iconify-icon>
                    {{ __('Pay Provider') }}
                </button>
            </div>
        </div>

        <!-- Earnings Table -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Provider Earnings') }}</h3>
                
                <!-- Status Filter -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Filter:') }}</label>
                    <select wire:model.live="statusFilter" class="form-control form-control-sm w-auto">
                        <option value="unpaid">{{ __('Unpaid') }}</option>
                        <option value="paid">{{ __('Paid') }}</option>
                        <option value="reversed">{{ __('Reversed') }}</option>
                        <option value="all">{{ __('All') }}</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Order ID') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Service') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Type') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Order Date') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Provider Share') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @forelse ($earnings as $earning)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ $earning['order_id'] }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $earning['service_name'] }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $earning['order_type'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($earning['order_date'])->format('M d, Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                    ${{ number_format($earning['provider_amount'], 2) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-center">
                                    @switch($earning['provider_payment_status'])
                                        @case('paid')
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                {{ __('Paid') }}
                                            </span>
                                            @break
                                        @case('reversed')
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                {{ __('Reversed') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                {{ __('Unpaid') }}
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <iconify-icon icon="lucide:inbox" class="mx-auto mb-2 h-8 w-8 text-gray-300 dark:text-gray-600"></iconify-icon>
                                    <p>{{ __('No earnings found for the selected filters.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Payment Modal -->
    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="closePaymentModal">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Pay Provider') }}</h3>
                    <button type="button" wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <iconify-icon icon="lucide:x" class="h-5 w-5"></iconify-icon>
                    </button>
                </div>

                <!-- Payment Summary -->
                <div class="mb-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Provider:') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $selectedProvider['name'] ?? '' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Outstanding:') }}</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${{ number_format($summary['outstanding_balance'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Orders Included:') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $summary['unpaid_orders_count'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form wire:submit.prevent="processPayment" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Payment Method') }} <span class="text-red-500">*</span></label>
                        <select wire:model="paymentMethod" class="form-control" required>
                            <option value="">{{ __('Select payment method') }}</option>
                            <option value="evc">EVC Plus</option>
                            <option value="zaad">ZAAD</option>
                            <option value="sahal">Sahal</option>
                            <option value="manual">{{ __('Manual/Cash') }}</option>
                        </select>
                        @error('paymentMethod') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Transaction Reference') }}</label>
                        <input type="text" wire:model="transactionReference" class="form-control" placeholder="{{ __('Enter transaction reference') }}">
                        @error('transactionReference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Notes') }}</label>
                        <textarea wire:model="paymentNotes" class="form-control" rows="3" placeholder="{{ __('Optional notes...') }}"></textarea>
                        @error('paymentNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Warning -->
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-900/20">
                        <div class="flex">
                            <iconify-icon icon="lucide:alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400"></iconify-icon>
                            <p class="ml-2 text-sm text-yellow-700 dark:text-yellow-300">
                                {{ __('This action will mark all outstanding provider earnings as PAID. This cannot be undone.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closePaymentModal" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="processPayment">
                                <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Confirm Payment') }}
                            </span>
                            <span wire:loading wire:target="processPayment">
                                {{ __('Processing...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
