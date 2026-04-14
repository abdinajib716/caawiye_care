<div class="space-y-6" x-data="scanImagingBooking()">
    {{-- Alpine.js Horizontal Stepper --}}
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

    {{-- Error Messages --}}
    @if(session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Step 1: Select Customer --}}
    <div x-show="currentStepNum === 1" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Customer Information') }}</h3>

        <div class="space-y-6">
            @if (!$showNewCustomerForm)
                <!-- Customer Dropdown -->
                <div>
                    <label class="form-label">
                        {{ __('Select Customer') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="customerId" class="form-control w-full">
                        <option value="">{{ __('Select a customer') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer['id'] }}">{{ $customer['name'] }} ({{ $customer['phone'] ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
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

            @if(session()->has('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Step 2: Select Service & Patient Info --}}
    <div x-show="currentStepNum === 2" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Select Service & Patient Info') }}</h3>
        
        <div class="space-y-6">
            {{-- Patient Name --}}
            <div>
                <label class="form-label">{{ __('Patient Name') }}</label>
                <input type="text" wire:model="patientName" class="form-control" placeholder="{{ __('Enter patient name (optional)') }}">
            </div>

            {{-- Service Selection --}}
            <div>
                <label class="form-label">{{ __('Select Service') }}</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto">
                    @foreach($services as $service)
                        <div wire:click="$set('serviceId', {{ $service['id'] }})" 
                            class="border {{ $serviceId === $service['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }} rounded-md p-4 cursor-pointer hover:border-blue-400 dark:hover:border-blue-600 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $service['service_name'] ?? $service['name'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $service['provider']['name'] ?? '' }}</div>
                                    <div class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-2">${{ number_format((float)$service['cost'], 2) }}</div>
                                </div>
                                @if($serviceId === $service['id'])
                                    <iconify-icon icon="lucide:check-circle-2" class="w-6 h-6 text-blue-600"></iconify-icon>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

                    {{-- Selected Service Summary --}}
                    @if($selectedService)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Selected Service') }}</div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Service Cost') }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($selectedService->cost, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Commission') }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($selectedService->commission_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-base font-bold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                    <span class="text-gray-900 dark:text-white">{{ __('Total Amount') }}</span>
                                    <span class="text-blue-600 dark:text-blue-400">${{ number_format($selectedService->total_with_commission, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
        </div>
    </div>

    {{-- Step 3: Schedule Appointment & Review --}}
    <div x-show="currentStepNum === 3" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Schedule Appointment & Review') }}</h3>
        
        <div class="space-y-6">
            {{-- Appointment Time --}}
            <div>
                <label class="form-label">{{ __('Appointment Date & Time') }}</label>
                <input type="datetime-local" wire:model="appointmentTime" class="form-control w-full">
            </div>

            {{-- Notes --}}
            <div>
                <label class="form-label">{{ __('Notes') }}</label>
                <textarea wire:model="notes" rows="4" 
                    class="form-control-textarea w-full"
                    placeholder="{{ __('Add any notes...') }}"></textarea>
            </div>

            {{-- Final Summary --}}
            @if($selectedService)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-6">
                    <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">{{ __('Booking Summary') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Service') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $selectedService->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700 dark:text-blue-300">{{ __('Provider') }}</span>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ $selectedService->provider->name }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-base border-t border-blue-200 dark:border-blue-700 pt-2 mt-2">
                            <span class="text-blue-900 dark:text-blue-100">{{ __('Total Amount') }}</span>
                            <span class="text-blue-600 dark:text-blue-400">${{ number_format($selectedService->total_with_commission, 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex items-center justify-between">
        <div>
            <button type="button" x-show="currentStepNum > 1" @click="previousStep()" class="btn btn-secondary">
                <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Previous') }}
            </button>
            <a x-show="currentStepNum === 1" href="{{ route('admin.scan-imaging-bookings.index') }}" class="btn btn-secondary">
                <iconify-icon icon="lucide:x" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Cancel') }}
            </a>
        </div>

        <div>
            <button type="button" x-show="currentStepNum < 3" @click="nextStep()" class="btn btn-primary">
                {{ __('Next') }}
                <iconify-icon icon="lucide:arrow-right" class="ml-2 h-4 w-4"></iconify-icon>
            </button>
            <button type="button" x-show="currentStepNum === 3" wire:click="submit" class="btn btn-success">
                <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Create Booking') }}
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function scanImagingBooking() {
            return {
                currentStepNum: @entangle('currentStep'),
                steps: [
                    { label: '{{ __("Customer") }}', description: '{{ __("Select customer") }}' },
                    { label: '{{ __("Service") }}', description: '{{ __("Choose service") }}' },
                    { label: '{{ __("Review") }}', description: '{{ __("Confirm booking") }}' }
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
