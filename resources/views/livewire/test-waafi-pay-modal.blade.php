<div>
    @if($showModal)
    <!-- Modal Backdrop -->
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        role="dialog"
        aria-modal="true"
        wire:key="test-waafipay-modal"
    >
        <!-- Modal Container -->
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            @click.away="$wire.closeModal()"
            class="w-full max-w-lg rounded-lg bg-white shadow-2xl dark:bg-gray-800"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('Test WaafiPay Payment') }}
                </h3>
                <button
                    wire:click="closeModal"
                    type="button"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors"
                >
                    <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-5 space-y-5">
                <!-- Phone Number Input -->
                <div>
                    <label for="test_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Phone Number') }}
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 text-sm font-medium text-gray-700 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            +252
                        </span>
                        <input
                            type="text"
                            id="test_phone"
                            wire:model.live="phone"
                            placeholder="619821172"
                            maxlength="9"
                            class="form-control rounded-none rounded-r-lg @error('phone') border-red-500 @enderror"
                        >
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Enter phone number without country code (e.g. 619821172)') }}
                    </p>
                    @error('phone')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center">
                            <iconify-icon icon="lucide:alert-circle" class="w-3 h-3 mr-1"></iconify-icon>
                            {{ $message }}
                        </p>
                    @enderror

                    @if($phone && strlen($phone) >= 9)
                        @php
                            $formattedPhone = app(\App\Services\WaafipayService::class)->formatPhoneNumber($phone);
                            $isValid = app(\App\Services\WaafipayService::class)->validatePhoneNumber($phone);
                            $provider = app(\App\Services\WaafipayService::class)->getProviderFromPhone($phone);
                        @endphp
                        @if($isValid)
                            <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-md bg-green-50 dark:bg-green-900/20 text-xs font-medium text-green-700 dark:text-green-400">
                                <iconify-icon icon="lucide:check-circle" class="w-3.5 h-3.5 mr-1.5"></iconify-icon>
                                {{ __('Valid') }} - {{ $provider }}
                            </div>
                        @else
                            <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-md bg-red-50 dark:bg-red-900/20 text-xs font-medium text-red-700 dark:text-red-400">
                                <iconify-icon icon="lucide:x-circle" class="w-3.5 h-3.5 mr-1.5"></iconify-icon>
                                {{ __('Invalid Somalia mobile number') }}
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Amount Input -->
                <div>
                    <label for="test_amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Amount') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400">
                            $
                        </span>
                        <input
                            type="number"
                            id="test_amount"
                            wire:model="amount"
                            step="0.01"
                            min="0.01"
                            max="1000"
                            class="form-control pl-7 @error('amount') border-red-500 @enderror"
                        >
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Test amount (max $1000)') }}
                    </p>
                    @error('amount')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center">
                            <iconify-icon icon="lucide:alert-circle" class="w-3 h-3 mr-1"></iconify-icon>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Processing State -->
                @if($processing)
                <div class="flex items-center justify-center space-x-3 py-4 px-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ $processingMessage }}</span>
                </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700 rounded-b-lg">
                <button
                    type="button"
                    wire:click="closeModal"
                    wire:loading.attr="disabled"
                    class="btn-secondary"
                >
                    {{ __('Cancel') }}
                </button>
                <button
                    type="button"
                    wire:click="sendPayment"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    <iconify-icon icon="lucide:send" class="w-4 h-4 mr-2"></iconify-icon>
                    {{ __('Send Payment') }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
