<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Customer Information') }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Step 2 of 3') }}
        </span>
    </div>

    <!-- Success Message -->
    @if(session()->has('customer-saved'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <div class="flex items-center">
                <iconify-icon icon="lucide:check-circle" class="mr-2 h-5 w-5 text-green-600 dark:text-green-400"></iconify-icon>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('customer-saved') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Search Field with Add Button -->
    <div>
        <label for="search" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('Search Customer') }} <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <iconify-icon icon="lucide:search" class="h-4 w-4 text-gray-400"></iconify-icon>
                </div>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.500ms="search"
                    class="form-control pl-10 pr-10"
                    placeholder="{{ __('Search by phone or name') }}"
                    @disabled($customerFound)
                />
                @if($customerFound)
                    <button
                        type="button"
                        wire:click="clearCustomer"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        title="{{ __('Clear selection') }}"
                    >
                        <iconify-icon icon="lucide:x-circle" class="h-4 w-4 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"></iconify-icon>
                    </button>
                @endif
            </div>
            <button
                type="button"
                wire:click="toggleNewCustomerForm"
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                title="{{ $showNewCustomerForm ? __('Hide form') : __('Add new customer') }}"
            >
                <iconify-icon icon="{{ $showNewCustomerForm ? 'lucide:x' : 'lucide:plus' }}" class="h-5 w-5"></iconify-icon>
            </button>
        </div>
        @error('search')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Matching Customers List -->
    @if(!$showNewCustomerForm && !$customerFound && count($matchingCustomers) > 0)
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <h4 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                {{ __('Select Customer') }}
            </h4>
            <div class="max-h-64 space-y-2 overflow-y-auto">
                @foreach($matchingCustomers as $customer)
                    <button
                        type="button"
                        wire:click="selectCustomer({{ $customer['id'] }})"
                        class="w-full rounded-lg border border-gray-200 bg-white p-3 text-left transition-colors hover:border-blue-300 hover:bg-blue-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-blue-600 dark:hover:bg-gray-600"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <iconify-icon icon="lucide:phone" class="inline h-3.5 w-3.5"></iconify-icon>
                                    {{ $customer['phone'] }}
                                </p>
                                @if(!empty($customer['address']))
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        <iconify-icon icon="lucide:map-pin" class="inline h-3.5 w-3.5"></iconify-icon>
                                        {{ $customer['address'] }}
                                    </p>
                                @endif
                            </div>
                            <iconify-icon icon="lucide:chevron-right" class="h-5 w-5 flex-shrink-0 text-gray-400"></iconify-icon>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Customer Status -->
    @if($customerFound)
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:user-check" class="mr-2 h-5 w-5 text-green-600 dark:text-green-400"></iconify-icon>
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ __('Customer Selected') }}
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-300">
                            {{ $name }}
                        </p>
                    </div>
                </div>
                @if($detectedProvider)
                    <div class="flex items-center space-x-2">
                        @php
                            $logoMap = [
                                'EVC PLUS' => 'evcplus.png',
                                'JEEB' => 'jeeb.png',
                                'ZAAD' => 'Zaad.png',
                                'SAHAL' => 'Sahal.png',
                            ];
                            $logo = $logoMap[$detectedProvider] ?? null;
                        @endphp
                        @if($logo)
                            <img
                                src="{{ asset('images/waafi/providers-telecome/' . $logo) }}"
                                alt="{{ $detectedProvider }}"
                                class="h-8 w-auto object-contain"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- New Customer Form -->
    @if($showNewCustomerForm)
    <div class="space-y-4 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20">
        <div class="mb-3 flex items-center">
            <iconify-icon icon="lucide:user-plus" class="mr-2 h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200">
                {{ __('New Customer Details') }}
            </h4>
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Phone Number') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <iconify-icon icon="lucide:phone" class="h-5 w-5 text-gray-400"></iconify-icon>
                </div>
                <input
                    type="text"
                    id="phone"
                    wire:model.blur="phone"
                    class="form-control pl-10"
                    placeholder="{{ __('Enter phone number (e.g., 61XXXXXXX)') }}"
                />
            </div>
            @error('phone')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Provider Detection -->
        @if($detectedProvider)
            <div class="rounded-lg border border-blue-300 bg-white p-3 dark:border-blue-600 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <iconify-icon icon="lucide:info" class="h-4 w-4 text-blue-600 dark:text-blue-400"></iconify-icon>
                        <div>
                            <p class="text-xs font-medium text-blue-800 dark:text-blue-200">
                                {{ __('Detected Provider') }}
                            </p>
                            <p class="text-xs text-blue-600 dark:text-blue-300">
                                {{ $detectedProvider }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @php
                            $logoMap = [
                                'EVC PLUS' => 'evcplus.png',
                                'JEEB' => 'jeeb.png',
                                'ZAAD' => 'Zaad.png',
                                'SAHAL' => 'Sahal.png',
                            ];
                            $logo = $logoMap[$detectedProvider] ?? null;
                        @endphp
                        @if($logo)
                            <img
                                src="{{ asset('images/waafi/providers-telecome/' . $logo) }}"
                                alt="{{ $detectedProvider }}"
                                class="h-8 w-auto object-contain"
                            />
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Name -->
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Customer Name') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                wire:model="name"
                class="form-control"
                placeholder="{{ __('Enter customer name') }}"
            />
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Address') }}
            </label>
            <textarea
                id="address"
                wire:model="address"
                rows="2"
                class="form-control"
                placeholder="{{ __('Enter customer address (optional)') }}"
            ></textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Save New Customer Button -->
        <button
            type="button"
            wire:click="saveCustomer"
            class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
        >
            <iconify-icon icon="lucide:save" class="mr-2 inline-block h-4 w-4"></iconify-icon>
            {{ __('Save Customer') }}
        </button>
    </div>
    @endif
</div>
