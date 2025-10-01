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

    <!-- Process Order Button -->
    <button
        type="button"
        wire:click="processOrder"
        @disabled(!$canProcess || $processing)
        class="w-full rounded-lg bg-blue-600 px-4 py-3 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-500 dark:hover:bg-blue-600"
    >
        @if($processing)
            <iconify-icon icon="lucide:loader-2" class="mr-2 inline-block h-5 w-5 animate-spin"></iconify-icon>
            {{ __('Processing...') }}
        @else
            <iconify-icon icon="lucide:check-circle" class="mr-2 inline-block h-5 w-5"></iconify-icon>
            {{ __('Process Order') }}
        @endif
    </button>

    @if(!$canProcess && !$processing)
        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('Complete all steps to process the order') }}
        </p>
    @endif
</div>
