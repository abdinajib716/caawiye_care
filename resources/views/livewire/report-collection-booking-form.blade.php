<div class="space-y-6" x-data="reportCollectionBooking()">
    {{-- Horizontal Stepper --}}
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <template x-for="(step, index) in steps" :key="index">
                <div class="flex flex-1 items-center">
                    <div class="flex items-center cursor-pointer" @click="goToStep(index + 1)">
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

    {{-- Error/Success Messages --}}
    @if(session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif
    @if(session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Step 1: Customer & Patient Information --}}
    <div x-show="currentStepNum === 1" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            <iconify-icon icon="lucide:user" class="inline mr-2"></iconify-icon>
            {{ __('Customer & Patient Information') }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Customer Section --}}
            <div class="space-y-4">
                <h4 class="font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Customer Information') }}</h4>

                <div>
                    <label class="form-label">{{ __('Fetch From Medicine Booking') }}</label>
                    <select wire:model.live="medicineOrderId" class="form-control w-full">
                        <option value="">{{ __('Select a medicine booking (optional)') }}</option>
                        @foreach($medicineOrders as $medicineOrder)
                            <option value="{{ $medicineOrder['id'] }}">{{ $medicineOrder['label'] }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('This prefills customer, supplier, and delivery details from the medicine module.') }}</p>
                </div>
                
                {{-- Customer Dropdown --}}
                @if (!$showNewCustomerForm)
                    <div>
                        <label class="form-label">{{ __('Select Customer') }} <span class="text-red-500">*</span></label>
                        <select wire:model="customerId" class="form-control w-full">
                            <option value="">{{ __('Select a customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer['id'] }}">{{ $customer['name'] }} ({{ $customer['phone'] ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        @error('customerId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- New Customer Form Toggle --}}
                @if (!$customerId)
                    <div>
                        <button type="button" wire:click="toggleNewCustomerForm" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                            @if ($showNewCustomerForm)
                                <iconify-icon icon="lucide:x" class="inline mr-1"></iconify-icon>
                                {{ __('Cancel - Select Existing Customer') }}
                            @else
                                <iconify-icon icon="lucide:plus" class="inline mr-1"></iconify-icon>
                                {{ __('Create New Customer') }}
                            @endif
                        </button>
                    </div>
                @endif

                {{-- New Customer Form --}}
                @if ($showNewCustomerForm)
                    <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div>
                            <label class="form-label">{{ __('Customer Full Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="customerName" class="form-control" placeholder="{{ __('Enter customer name') }}">
                            @error('customerName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">{{ __('Customer Phone Number') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="customerPhone" class="form-control" placeholder="{{ __('e.g., 0613048163') }}">
                            @error('customerPhone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="button" wire:click="saveNewCustomer" class="btn btn-secondary btn-sm">
                            <iconify-icon icon="lucide:save" class="mr-1"></iconify-icon>
                            {{ __('Save Customer') }}
                        </button>
                    </div>
                @endif
            </div>

            {{-- Patient Section --}}
            <div class="space-y-4">
                <h4 class="font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Patient Information') }}</h4>
                
                <div>
                    <label class="form-label">{{ __('Patient Full Name') }} <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="patientName" class="form-control" placeholder="{{ __('Enter patient name') }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('Used to identify the exact medical report being collected') }}</p>
                    @error('patientName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Patient Reference / File Number') }} <span class="text-gray-400">({{ __('Optional') }})</span></label>
                    <input type="text" wire:model="patientReference" class="form-control" placeholder="{{ __('Enter file number if available') }}">
                    @error('patientReference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Provider Information --}}
    <div x-show="currentStepNum === 2" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            <iconify-icon icon="lucide:building" class="inline mr-2"></iconify-icon>
            {{ __('Provider Information') }}
        </h3>

        <div class="space-y-6">
            <div>
                <label class="form-label">{{ __('Provider Type') }} <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($providerTypes as $key => $label)
                        <div 
                            wire:click="$set('providerType', '{{ $key }}')" 
                            class="border rounded-lg p-4 cursor-pointer transition-all text-center {{ $providerType === $key ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}"
                        >
                            <div class="flex flex-col items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $providerType === $key ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    @if($key === 'hospital')
                                        <iconify-icon icon="lucide:hospital" class="h-5 w-5"></iconify-icon>
                                    @elseif($key === 'laboratory')
                                        <iconify-icon icon="lucide:flask-conical" class="h-5 w-5"></iconify-icon>
                                    @elseif($key === 'supplier')
                                        <iconify-icon icon="lucide:truck" class="h-5 w-5"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:building-2" class="h-5 w-5"></iconify-icon>
                                    @endif
                                </div>
                                <span class="mt-2 text-sm font-medium {{ $providerType === $key ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">{{ __($label) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('providerType') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">{{ __('Provider Name') }} <span class="text-red-500">*</span></label>
                <input type="text" wire:model="providerName" class="form-control" placeholder="{{ __('Enter provider/facility name') }}">
                @error('providerName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">{{ __('Provider Address / Location') }} <span class="text-gray-400">({{ __('Optional') }})</span></label>
                <textarea wire:model="providerAddress" rows="2" class="form-control-textarea w-full" placeholder="{{ __('Enter address or location details') }}"></textarea>
                @error('providerAddress') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Step 3: Collection Details & Assignment --}}
    <div x-show="currentStepNum === 3" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            <iconify-icon icon="lucide:clipboard-list" class="inline mr-2"></iconify-icon>
            {{ __('Collection Details & Assignment') }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Collection Details --}}
            <div class="space-y-4">
                <h4 class="font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Collection Details') }}</h4>

                <div>
                    <label class="form-label">{{ __('Delivery Required') }}</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.live="deliveryRequired" value="1" class="form-radio">
                            <span class="ml-2">{{ __('Yes') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.live="deliveryRequired" value="0" class="form-radio">
                            <span class="ml-2">{{ __('No') }}</span>
                        </label>
                    </div>
                </div>

                @if($deliveryRequired)
                    <div class="space-y-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label">{{ __('Pick-up Location') }} <span class="text-red-500">*</span></label>
                                <select wire:model.live="pickupLocationId" class="form-control w-full">
                                    <option value="">{{ __('Select pick-up location') }}</option>
                                    @foreach($deliveryLocations as $location)
                                        <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('pickupLocationId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">{{ __('Drop-off Location') }} <span class="text-red-500">*</span></label>
                                <select wire:model.live="dropoffLocationId" class="form-control w-full">
                                    <option value="">{{ __('Select drop-off location') }}</option>
                                    @foreach($deliveryLocations as $location)
                                        <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('dropoffLocationId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if ($deliveryFee > 0)
                            <div class="rounded-md bg-green-50 p-3 dark:bg-green-900/20">
                                <p class="text-sm text-green-700 dark:text-green-400">
                                    <iconify-icon icon="lucide:check-circle" class="mr-1 inline h-4 w-4"></iconify-icon>
                                    {{ __('Delivery Price:') }} <span class="font-bold">${{ number_format($deliveryFee, 2) }}</span>
                                </p>
                            </div>
                        @elseif ($deliveryPriceMessage)
                            <div class="rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/20">
                                <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                    <iconify-icon icon="lucide:alert-triangle" class="mr-1 inline h-4 w-4"></iconify-icon>
                                    {{ $deliveryPriceMessage }}
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">{{ __('Delivery Date') }} <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="deliveryDate" class="form-control" min="{{ date('Y-m-d') }}">
                            @error('deliveryDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">{{ __('Delivery Time') }} <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="deliveryTime" class="form-control">
                            @error('deliveryTime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    </div>
                @endif

                <div>
                    <label class="form-label">{{ __('Internal Notes') }} <span class="text-gray-400">({{ __('Optional') }})</span></label>
                    <textarea wire:model="internalNotes" rows="3" class="form-control-textarea w-full" placeholder="{{ __('Add any internal notes...') }}"></textarea>
                    @error('internalNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Assignment --}}
            <div class="space-y-4">
                <h4 class="font-medium text-gray-700 dark:text-gray-300 border-b pb-2">{{ __('Assignment (Internal Tracking)') }}</h4>

                <div>
                    <label class="form-label">{{ __('Assigned Staff') }} <span class="text-red-500">*</span></label>
                    <select wire:model="assignedStaffId" class="form-control w-full">
                        <option value="">{{ __('Select staff member') }}</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff['id'] }}">{{ $staff['name'] }}</option>
                        @endforeach
                    </select>
                    @error('assignedStaffId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Assignment Notes') }} <span class="text-gray-400">({{ __('Optional') }})</span></label>
                    <textarea wire:model="assignmentNotes" rows="3" class="form-control-textarea w-full" placeholder="{{ __('Add assignment instructions...') }}"></textarea>
                    @error('assignmentNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Charges Summary --}}
                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4 mt-4">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Charges') }}</div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Base Service Charge') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($serviceCharge, 2) }}</span>
                        </div>
                        @if($deliveryRequired)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('Delivery Fee') }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($deliveryFee, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="text-gray-900 dark:text-white">{{ __('Total Amount') }}</span>
                            <span class="text-blue-600 dark:text-blue-400">${{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: Review & Payment --}}
    <div x-show="currentStepNum === 4" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
            <iconify-icon icon="lucide:clipboard-check" class="inline mr-2"></iconify-icon>
            {{ __('Review & Process') }}
        </h3>

        <div class="space-y-6">
            {{-- Payment Method Selection --}}
            <div>
                <label class="form-label">{{ __('Payment Method') }} <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($paymentMethods as $key => $label)
                        <div 
                            wire:click="$set('paymentMethod', '{{ $key }}')" 
                            class="border rounded-lg p-4 cursor-pointer transition-all {{ $paymentMethod === $key ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-medium {{ $paymentMethod === $key ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }}">{{ $label }}</span>
                                @if($paymentMethod === $key)
                                    <iconify-icon icon="lucide:check-circle-2" class="h-5 w-5 text-blue-500"></iconify-icon>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('paymentMethod') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Final Summary --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-6">
                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">{{ __('Request Summary') }}</h4>
                
                @php
                    $selectedCustomer = collect($customers)->firstWhere('id', $customerId);
                    $displayCustomerName = $selectedCustomer ? $selectedCustomer['name'] : ($customerName ?: 'N/A');
                    $displayCustomerPhone = $selectedCustomer ? ($selectedCustomer['phone'] ?? 'N/A') : ($customerPhone ?: 'N/A');
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Left Column --}}
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Customer Name') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $displayCustomerName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Customer Phone') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $displayCustomerPhone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Patient Name') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $patientName ?: 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Provider') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $providerName ?: 'N/A' }} ({{ $providerTypes[$providerType] ?? '' }})</span>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Assigned Staff') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $this->getAssignedStaffName() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Delivery Required') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $deliveryRequired ? __('Yes') : __('No') }}</span>
                        </div>
                        @if($deliveryRequired)
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-300">{{ __('Delivery Date/Time') }}</span>
                                <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $deliveryDate }} {{ $deliveryTime }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Payment Method') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $paymentMethods[$paymentMethod] ?? '' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex justify-between font-bold text-lg border-t border-blue-200 dark:border-blue-700 pt-4 mt-4">
                    <span class="text-blue-900 dark:text-blue-100">{{ __('Total Amount') }}</span>
                    <span class="text-blue-600 dark:text-blue-400">${{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>

            {{-- Payment Notice --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                <div class="flex items-start">
                    <iconify-icon icon="lucide:alert-circle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5"></iconify-icon>
                    <div>
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            {{ __('Payment will be verified via Mobile Money API. The customer will receive a payment prompt on their phone. Request will only be created after successful payment verification.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex items-center justify-between">
        <div>
            <button type="button" x-show="currentStepNum > 1" @click="previousStep()" class="btn btn-secondary">
                <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Previous') }}
            </button>
            <a x-show="currentStepNum === 1" href="{{ route('admin.collections.index') }}" class="btn btn-secondary">
                <iconify-icon icon="lucide:x" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Cancel') }}
            </a>
        </div>

        <div>
            <button type="button" x-show="currentStepNum < 4" @click="nextStep()" class="btn btn-primary">
                {{ __('Next') }}
                <iconify-icon icon="lucide:arrow-right" class="ml-2 h-4 w-4"></iconify-icon>
            </button>
            <button type="button" x-show="currentStepNum === 4" wire:click="submit" wire:loading.attr="disabled" class="btn btn-success">
                <span wire:loading.remove wire:target="submit">
                    <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Process & Create Request') }}
                </span>
                <span wire:loading wire:target="submit">
                    <iconify-icon icon="lucide:loader-2" class="mr-2 h-4 w-4 animate-spin"></iconify-icon>
                    {{ __('Processing Payment...') }}
                </span>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function reportCollectionBooking() {
            return {
                currentStepNum: @entangle('currentStep'),
                steps: [
                    { label: '{{ __("Customer & Patient") }}', description: '{{ __("Basic info") }}' },
                    { label: '{{ __("Provider") }}', description: '{{ __("Provider details") }}' },
                    { label: '{{ __("Details") }}', description: '{{ __("Collection & Assignment") }}' },
                    { label: '{{ __("Review") }}', description: '{{ __("Process request") }}' }
                ],
                nextStep() {
                    @this.call('nextStep');
                },
                previousStep() {
                    @this.call('previousStep');
                },
                goToStep(step) {
                    @this.call('goToStep', step);
                }
            }
        }
    </script>
    @endpush
</div>
