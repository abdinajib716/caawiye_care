<div class="space-y-6" x-data="medicineBooking()">
    <!-- Stepper - Matching Appointment Pattern -->
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <template x-for="(step, index) in steps" :key="index">
                <div class="flex flex-1 items-center">
                    <div class="flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-colors"
                            :class="currentStepNum > index + 1 ? 'border-green-500 bg-green-500 text-white' : currentStepNum === index + 1 ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-700'"
                        >
                            <template x-if="currentStepNum > index + 1">
                                <iconify-icon icon="lucide:check" class="h-5 w-5"></iconify-icon>
                            </template>
                            <template x-if="currentStepNum <= index + 1">
                                <span x-text="index + 1"></span>
                            </template>
                        </div>
                        <div class="ml-3 hidden sm:block">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="step.label"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="step.description"></p>
                        </div>
                    </div>
                    <div
                        x-show="index < steps.length - 1"
                        class="mx-4 h-0.5 flex-1 bg-gray-200 dark:bg-gray-700"
                        :class="currentStepNum > index + 1 ? 'bg-green-500' : ''"
                    ></div>
                </div>
            </template>
        </div>
    </div>

    <!-- General Error -->
    @if (!empty($validationErrors['general']))
        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
            <p class="text-sm text-red-600 dark:text-red-400">{{ $validationErrors['general'] }}</p>
        </div>
    @endif

    <!-- Step 1: Medicine & Delivery Details -->
    <div x-show="currentStepNum === 1" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Medicine & Delivery Details') }}</h3>
        
        <div class="space-y-6">
            <!-- Medicines -->
            <div>
                <label class="form-label">{{ __('Medicines') }} <span class="text-red-500">*</span></label>
                
                @foreach ($medicines as $index => $medicine)
                    <div class="mb-3 rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-800" wire:key="medicine-{{ $medicine['id'] }}">
                        <div class="flex items-start gap-3">
                            <!-- Medicine Name -->
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Medicine') }}</label>
                                <input type="text" 
                                       wire:model.live="medicines.{{ $index }}.medicine_name"
                                       class="form-control form-control-sm"
                                       placeholder="{{ __('Enter medicine name') }}">
                                <select wire:model.live="medicines.{{ $index }}.medicine_id" 
                                        class="form-control form-control-sm mt-1">
                                    <option value="">{{ __('Or select existing') }}</option>
                                    @foreach ($allMedicines as $med)
                                        <option value="{{ $med['id'] }}">{{ $med['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div style="width: 80px;">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Qty') }}</label>
                                <input type="number" 
                                       wire:model.live="medicines.{{ $index }}.quantity"
                                       min="1"
                                       class="form-control form-control-sm text-center">
                            </div>

                            <!-- Cost -->
                            <div style="width: 90px;">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Cost') }}</label>
                                <input type="number" 
                                       wire:model.live="medicines.{{ $index }}.cost"
                                       step="0.01"
                                       min="0"
                                       class="form-control form-control-sm">
                            </div>

                            <!-- Profit Type -->
                            <div style="width: 70px;">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Type') }}</label>
                                <select wire:model.live="medicines.{{ $index }}.profit_type"
                                        class="form-control form-control-sm text-xs">
                                    <option value="fixed">$</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>

                            <!-- Profit -->
                            <div style="width: 90px;">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ ($medicine['profit_type'] ?? 'fixed') === 'percentage' ? __('Profit %') : __('Profit $') }}
                                </label>
                                <input type="number" 
                                       wire:model.live="medicines.{{ $index }}.profit"
                                       step="0.01"
                                       min="0"
                                       max="{{ ($medicine['profit_type'] ?? 'fixed') === 'percentage' ? '100' : '' }}"
                                       class="form-control form-control-sm">
                            </div>

                            <!-- Total -->
                            <div style="width: 110px;">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Total') }}</label>
                                <div class="flex items-center h-9 rounded-md border border-gray-300 bg-gray-50 px-2 dark:border-gray-600 dark:bg-gray-700">
                                    @php
                                        $cost = (float)($medicine['cost'] ?? 0);
                                        $qty = (int)($medicine['quantity'] ?? 1);
                                        $profit = (float)($medicine['profit'] ?? 0);
                                        $profitType = $medicine['profit_type'] ?? 'fixed';
                                        $totalCost = $cost * $qty;
                                        $profitAmount = $profitType === 'percentage' ? ($totalCost * $profit / 100) : $profit;
                                        $total = $totalCost + $profitAmount;
                                    @endphp
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            @if (count($medicines) > 1)
                                <div class="flex items-end">
                                    <button type="button" 
                                            wire:click="removeMedicine({{ $medicine['id'] }})"
                                            class="flex h-9 w-9 items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors"
                                            title="{{ __('Remove') }}">
                                        <iconify-icon icon="lucide:trash-2" class="h-4 w-4"></iconify-icon>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addMedicine" class="btn btn-secondary">
                    <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Add Medicine') }}
                </button>

                @if (!empty($validationErrors['medicines']))
                    <p class="mt-1 text-sm text-red-600">{{ $validationErrors['medicines'] }}</p>
                @endif
            </div>

            <!-- Supplier -->
            <div>
                <label class="form-label">{{ __('Supplier') }} <span class="text-red-500">*</span></label>
                <select wire:model.live="supplierId" class="form-control" required>
                    <option value="">{{ __('Select a supplier') }}</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                    @endforeach
                </select>
                @if (!empty($validationErrors['supplier']))
                    <p class="mt-1 text-sm text-red-600">{{ $validationErrors['supplier'] }}</p>
                @endif
            </div>

            <!-- Delivery Option -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="requiresDelivery" class="form-checkbox">
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Requires Delivery') }}</span>
                </label>
            </div>

            @if ($requiresDelivery)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <!-- Pickup Location -->
                    <div>
                        <label class="form-label">{{ __('Pick-up Location') }} <span class="text-red-500">*</span></label>
                        <select wire:model.live="pickupLocationId" class="form-control">
                            <option value="">{{ __('Select pick-up location') }}</option>
                            @foreach ($deliveryLocations as $location)
                                <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                            @endforeach
                        </select>
                        @if (!empty($validationErrors['pickup']))
                            <p class="mt-1 text-sm text-red-600">{{ $validationErrors['pickup'] }}</p>
                        @endif
                    </div>

                    <!-- Dropoff Location -->
                    <div>
                        <label class="form-label">{{ __('Drop-off Location') }} <span class="text-red-500">*</span></label>
                        <select wire:model.live="dropoffLocationId" class="form-control">
                            <option value="">{{ __('Select drop-off location') }}</option>
                            @foreach ($deliveryLocations as $location)
                                <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                            @endforeach
                        </select>
                        @if (!empty($validationErrors['dropoff']))
                            <p class="mt-1 text-sm text-red-600">{{ $validationErrors['dropoff'] }}</p>
                        @endif
                    </div>

                    <!-- Delivery Price Display -->
                    @if ($deliveryPrice !== null)
                        <div class="col-span-1 md:col-span-2">
                            <div class="rounded-md bg-green-50 p-3 dark:bg-green-900/20">
                                <p class="text-sm text-green-700 dark:text-green-400">
                                    <iconify-icon icon="lucide:check-circle" class="mr-1 inline h-4 w-4"></iconify-icon>
                                    {{ __('Delivery Price:') }} <span class="font-bold">${{ number_format($deliveryPrice, 2) }}</span>
                                </p>
                            </div>
                        </div>
                    @elseif ($deliveryPriceMessage)
                        <div class="col-span-1 md:col-span-2">
                            <div class="rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/20">
                                <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                    <iconify-icon icon="lucide:alert-triangle" class="mr-1 inline h-4 w-4"></iconify-icon>
                                    {{ $deliveryPriceMessage }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Step 2: Customer Information -->
    <div x-show="currentStepNum === 2" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Customer Information') }}</h3>

        <div class="space-y-6">
            @if (!$showNewCustomerForm)
                <!-- Customer Searchable Dropdown -->
                <x-livewire-searchable-select
                    :label="__('Search Customer')"
                    :placeholder="__('Select a customer')"
                    searchModel="customerSearch"
                    :options="$matchingCustomers"
                    :selectedValue="$customerId"
                    :selectedDisplay="$customerName ? $customerName . ' (' . $customerPhone . ')' : ''"
                    onSelect="selectCustomer"
                    :required="true"
                    icon="lucide:user"
                />
            @endif

            <!-- New Customer Form Toggle -->
            @if (!$customerId)
                <div>
                    <button type="button" wire:click="toggleNewCustomerForm" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                        @if ($showNewCustomerForm)
                            <iconify-icon icon="lucide:x" class="inline h-4 w-4"></iconify-icon> {{ __('Cancel') }}
                        @else
                            <iconify-icon icon="lucide:plus" class="inline h-4 w-4"></iconify-icon> {{ __('Create New Customer') }}
                        @endif
                    </button>
                </div>
            @endif

            <!-- New Customer Form -->
            @if ($showNewCustomerForm)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-4">
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('New Customer Details') }}</h4>
                    
                    <div>
                        <label class="form-label">{{ __('Customer Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="customerName" class="form-control" placeholder="{{ __('Enter customer name') }}">
                        @error('customerName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="customerPhone" class="form-control" placeholder="{{ __('Enter phone number') }}">
                        @error('customerPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="button" wire:click="saveNewCustomer" class="btn btn-primary w-full">
                        <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Save Customer') }}
                    </button>
                </div>
            @endif

            @if (!empty($validationErrors['customer']))
                <p class="text-sm text-red-600">{{ $validationErrors['customer'] }}</p>
            @endif
        </div>
    </div>

    <!-- Step 3: Review & Process -->
    <div x-show="currentStepNum === 3" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Review & Process') }}</h3>
        
        <div class="space-y-6">
            <!-- Medicines Summary -->
            <div>
                <h4 class="mb-3 font-medium text-gray-900 dark:text-white">{{ __('Medicines') }}</h4>
                <div class="space-y-2">
                    @foreach ($medicines as $medicine)
                        @php
                            $cost = (float)($medicine['cost'] ?? 0);
                            $qty = (int)($medicine['quantity'] ?? 1);
                            $profit = (float)($medicine['profit'] ?? 0);
                            $profitType = $medicine['profit_type'] ?? 'fixed';
                            $totalCost = $cost * $qty;
                            $profitAmount = $profitType === 'percentage' ? ($totalCost * $profit / 100) : $profit;
                            $total = $totalCost + $profitAmount;
                        @endphp
                        <div class="flex justify-between text-sm">
                            <span>{{ $medicine['medicine_name'] }} × {{ $medicine['quantity'] }}</span>
                            <span class="font-medium">${{ number_format($total, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Summary -->
            @if ($requiresDelivery && $deliveryPrice)
                <div>
                    <h4 class="mb-3 font-medium text-gray-900 dark:text-white">{{ __('Delivery') }}</h4>
                    <div class="flex justify-between text-sm">
                        <span>
                            {{ collect($deliveryLocations)->firstWhere('id', $pickupLocationId)['name'] ?? '' }}
                            →
                            {{ collect($deliveryLocations)->firstWhere('id', $dropoffLocationId)['name'] ?? '' }}
                        </span>
                        <span class="font-medium">${{ number_format($deliveryPrice, 2) }}</span>
                    </div>
                </div>
            @endif

            <!-- Customer Summary -->
            <div>
                <h4 class="mb-3 font-medium text-gray-900 dark:text-white">{{ __('Customer') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customerName }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customerPhone }}</p>
            </div>

            <!-- Total Breakdown -->
            <div class="border-t border-gray-200 pt-4 dark:border-gray-700 space-y-2">
                @php
                    $medicinesTotal = collect($medicines)->sum(function($m) {
                        $cost = (float)($m['cost'] ?? 0);
                        $qty = (int)($m['quantity'] ?? 1);
                        $profit = (float)($m['profit'] ?? 0);
                        $profitType = $m['profit_type'] ?? 'fixed';
                        $totalCost = $cost * $qty;
                        $profitAmount = $profitType === 'percentage' ? ($totalCost * $profit / 100) : $profit;
                        return $totalCost + $profitAmount;
                    });
                @endphp
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Subtotal (Medicines):') }}</span>
                    <span class="font-medium">${{ number_format($medicinesTotal, 2) }}</span>
                </div>
                @if ($requiresDelivery && $deliveryPrice)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('Delivery:') }}</span>
                        <span class="font-medium">${{ number_format($deliveryPrice, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 dark:border-gray-700">
                    <span>{{ __('Total:') }}</span>
                    <span class="text-primary-600">${{ number_format($this->calculateTotal(), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex justify-between">
        <div>
            <button type="button" x-show="currentStepNum > 1" @click="previousStep()" class="btn btn-secondary">
                <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Previous') }}
            </button>
        </div>

        <div>
            <button type="button" x-show="currentStepNum < 3" @click="nextStep()" class="btn btn-primary">
                {{ __('Next') }}
                <iconify-icon icon="lucide:arrow-right" class="ml-2 h-4 w-4"></iconify-icon>
            </button>
            <button type="button" x-show="currentStepNum === 3" wire:click="submitOrder" wire:loading.attr="disabled" wire:target="submitOrder" class="btn btn-success">
                <iconify-icon icon="lucide:credit-card" class="mr-2 h-4 w-4"></iconify-icon>
                <span wire:loading.remove wire:target="submitOrder">
                    {{ __('Pay') }} ${{ number_format($this->calculateTotal(), 2) }}
                </span>
                <span wire:loading wire:target="submitOrder">
                    {{ __('Processing...') }}
                </span>
            </button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div wire:loading.delay wire:target="submitOrder" 
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
         style="margin: 0; padding: 1rem; left: 0; right: 0; top: 0; bottom: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-8 w-full max-w-md mx-auto">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4 bg-blue-100 dark:bg-blue-900">
                    <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('Processing Payment') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Please wait while we process your payment...') }}</p>
                <div class="flex justify-center space-x-2 mb-4">
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></div>
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Please do not close this window') }}</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function medicineBooking() {
            return {
                currentStepNum: @entangle('currentStep'),
                steps: [
                    { label: '{{ __("Medicine") }}', description: '{{ __("Details") }}' },
                    { label: '{{ __("Customer") }}', description: '{{ __("Information") }}' },
                    { label: '{{ __("Review") }}', description: '{{ __("Confirm") }}' },
                ],
                nextStep() {
                    @this.call('nextStep');
                },
                previousStep() {
                    @this.call('previousStep');
                }
            }
        }
    </script>
    @endpush
</div>
