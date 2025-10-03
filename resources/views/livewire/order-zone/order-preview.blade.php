<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Order Preview') }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Step 3 of 3') }}
        </span>
    </div>

    <!-- Order Summary -->
    <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <!-- Customer Info -->
        @if($customer)
            <div class="border-b border-gray-200 pb-4 dark:border-gray-700">
                <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Customer') }}
                </h4>
                <div class="space-y-1">
                    <p class="text-sm text-gray-900 dark:text-white">
                        <iconify-icon icon="lucide:user" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                        {{ $customer['name'] }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <iconify-icon icon="lucide:phone" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                        {{ $customer['phone'] }}
                    </p>
                    @if(!empty($customer['address']))
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <iconify-icon icon="lucide:map-pin" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                            {{ $customer['address'] }}
                        </p>
                    @endif
                </div>
            </div>
        @else
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-800 dark:bg-yellow-900/20">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <iconify-icon icon="lucide:alert-triangle" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                    {{ __('Please select a customer') }}
                </p>
            </div>
        @endif

        <!-- Services List -->
        @if(!empty($services))
            <div class="border-b border-gray-200 pb-4 dark:border-gray-700">
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Services') }}
                </h4>
                <div class="space-y-2">
                    @foreach($services as $service)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $service['name'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    ${{ number_format($service['price'], 2) }} × {{ $service['quantity'] }}
                                </p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($service['total'], 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-800 dark:bg-yellow-900/20">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <iconify-icon icon="lucide:alert-triangle" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                    {{ __('Please select services') }}
                </p>
            </div>
        @endif

        <!-- Totals -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal') }}</span>
                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
            </div>
            @if($tax > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Tax') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($tax, 2) }}</span>
                </div>
            @endif
            @if($discount > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Discount') }}</span>
                    <span class="font-medium text-red-600 dark:text-red-400">-${{ number_format($discount, 2) }}</span>
                </div>
            @endif
            <div class="flex items-center justify-between border-t border-gray-200 pt-2 dark:border-gray-700">
                <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Total') }}</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Payment Method -->
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Payment Method') }}
        </h4>

        @if($waafipayEnabled || $edahabEnabled)
            <div class="space-y-2">
                @if($waafipayEnabled)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-700/50">
                        <div class="flex items-center space-x-3">
                            <iconify-icon icon="lucide:smartphone" class="h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('WaafiPay') }}</span>
                        </div>
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            {{ __('Enabled') }}
                        </span>
                    </div>
                @endif

                @if($edahabEnabled)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-700/50">
                        <div class="flex items-center space-x-3">
                            <iconify-icon icon="lucide:smartphone" class="h-5 w-5 text-orange-600 dark:text-orange-400"></iconify-icon>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('eDahab') }}</span>
                        </div>
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            {{ __('Enabled') }}
                        </span>
                    </div>
                @endif

                @if($provider)
                    <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/20">
                        <p class="text-xs text-blue-800 dark:text-blue-200">
                            <iconify-icon icon="lucide:info" class="mr-1 inline-block h-3 w-3"></iconify-icon>
                            {{ __('Provider: :provider', ['provider' => $provider]) }}
                        </p>
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">
                    <iconify-icon icon="lucide:alert-circle" class="mr-1 inline-block h-4 w-4"></iconify-icon>
                    {{ __('No payment methods enabled. Please enable WaafiPay or eDahab in settings.') }}
                </p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col gap-3">
        <!-- Test Modal Button (Remove after testing) -->
        <button
            type="button"
            wire:click="testModal"
            class="w-full rounded-lg border-2 border-purple-500 bg-purple-100 px-4 py-2 text-sm font-medium text-purple-700 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-200"
        >
            🧪 Test Modal (Debug)
        </button>

        <div class="flex gap-3">
            <!-- Cancel Button -->
            <button
                type="button"
                wire:click="cancelOrder"
                @disabled($processing)
                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                <iconify-icon icon="lucide:x" class="mr-2 inline-block h-5 w-5"></iconify-icon>
                {{ __('Cancel') }}
            </button>

            <!-- Pay Button -->
            <button
                type="button"
                wire:click="processOrder"
                {{ (!$canProcess || $processing) ? 'disabled' : '' }}
                class="flex-[2] rounded-lg px-4 py-3 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 {{ (!$canProcess || $processing) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500 dark:bg-green-500 dark:hover:bg-green-600' }}"
            >
                @if($processing)
                    <iconify-icon icon="lucide:loader-2" class="mr-2 inline-block h-5 w-5 animate-spin"></iconify-icon>
                    {{ __('Processing...') }}
                @else
                    <iconify-icon icon="lucide:credit-card" class="mr-2 inline-block h-5 w-5"></iconify-icon>
                    {{ __('Pay $:amount', ['amount' => number_format($total, 2)]) }}
                @endif
            </button>
        </div>
    </div>

    @if(!$canProcess && !$processing)
        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('Complete all steps to process the order') }}
        </p>
    @endif
</div>

<!-- Payment Progress Modal (Outside main component div for proper z-index) -->
@if($showPaymentModal)
    <div
        x-data="{ show: @entangle('showPaymentModal').live }"
        x-show="show"
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-80"
                aria-hidden="true"
            ></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle"
            >
                <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-16 sm:w-16">
                            <iconify-icon icon="lucide:loader-2" class="h-10 w-10 animate-spin text-blue-600 dark:text-blue-400"></iconify-icon>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                {{ __('Processing Payment') }}
                            </h3>
                            <div class="mt-4 space-y-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $paymentStatusMessage }}
                                </p>

                                <!-- Progress Steps -->
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $paymentStep >= 1 ? 'bg-green-500' : 'bg-gray-300' }}">
                                            @if($paymentStep >= 1)
                                                <iconify-icon icon="lucide:check" class="h-4 w-4 text-white"></iconify-icon>
                                            @else
                                                <span class="text-xs text-white">1</span>
                                            @endif
                                        </div>
                                        <span class="text-sm {{ $paymentStep >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ __('Payment request sent') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @php
                                            $step2Class = $paymentStep >= 2 ? 'bg-green-500' : ($paymentStep === 1 ? 'bg-blue-500 animate-pulse' : 'bg-gray-300');
                                        @endphp
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $step2Class }}">
                                            @if($paymentStep >= 2)
                                                <iconify-icon icon="lucide:check" class="h-4 w-4 text-white"></iconify-icon>
                                            @elseif($paymentStep === 1)
                                                <iconify-icon icon="lucide:loader-2" class="h-4 w-4 animate-spin text-white"></iconify-icon>
                                            @else
                                                <span class="text-xs text-white">2</span>
                                            @endif
                                        </div>
                                        <span class="text-sm {{ $paymentStep >= 2 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ __('Waiting for confirmation') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $paymentStep >= 3 ? 'bg-green-500' : 'bg-gray-300' }}">
                                            @if($paymentStep >= 3)
                                                <iconify-icon icon="lucide:check" class="h-4 w-4 text-white"></iconify-icon>
                                            @else
                                                <span class="text-xs text-white">3</span>
                                            @endif
                                        </div>
                                        <span class="text-sm {{ $paymentStep >= 3 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ __('Creating order') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Amount Display -->
                                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Amount') }}</span>
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Warning Message -->
                                <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-800 dark:bg-yellow-900/20">
                                    <p class="text-xs text-yellow-800 dark:text-yellow-200">
                                        <iconify-icon icon="lucide:alert-triangle" class="mr-1 inline-block h-3 w-3"></iconify-icon>
                                        {{ __('Please do not close this window or refresh the page.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
