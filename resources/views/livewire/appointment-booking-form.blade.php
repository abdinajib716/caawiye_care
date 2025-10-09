<div class="space-y-6" x-data="appointmentBooking()">
    <!-- Stepper - Matching OrderZone Pattern -->
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

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success">
            <div class="flex">
                <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($validationErrors['general']))
        <div class="alert alert-danger">
            <div class="flex">
                <iconify-icon icon="lucide:alert-circle" class="h-5 w-5"></iconify-icon>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ $validationErrors['general'] }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 1: Appointment Details -->
    <div x-show="currentStepNum === 1" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Appointment Details') }}</h3>

        <div class="space-y-6">
            <!-- Appointment Type -->
            <div>
                <label class="form-label">{{ __('Appointment Type') }} <span class="text-red-500">*</span></label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="appointmentType" value="self" class="form-radio">
                        <span class="ml-2 text-sm">{{ __('Self') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="appointmentType" value="someone_else" class="form-radio">
                        <span class="ml-2 text-sm">{{ __('Someone Else') }}</span>
                    </label>
                </div>
            </div>

            <!-- Patient Name (conditional) -->
            @if ($appointmentType === 'someone_else')
                <div>
                    <label class="form-label">{{ __('Patient Name') }} <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="patientName" class="form-control @error('patientName') border-red-500 @enderror" placeholder="{{ __('Enter patient name') }}">
                    @if (!empty($validationErrors['patientName']))
                        <p class="mt-1 text-sm text-red-600">{{ $validationErrors['patientName'] }}</p>
                    @endif
                </div>
            @endif

            <!-- Hospital Selection -->
            <div>
                <label class="form-label">{{ __('Hospital') }} <span class="text-red-500">*</span></label>
                <select wire:model.live="hospitalId" class="form-control @error('hospitalId') border-red-500 @enderror">
                    <option value="">{{ __('Select a hospital') }}</option>
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital['id'] }}">{{ $hospital['name'] }}</option>
                    @endforeach
                </select>
                @if (!empty($validationErrors['hospitalId']))
                    <p class="mt-1 text-sm text-red-600">{{ $validationErrors['hospitalId'] }}</p>
                @endif
            </div>

            <!-- Doctor Selection -->
            <div>
                <label class="form-label">{{ __('Doctor') }} <span class="text-red-500">*</span></label>
                <select wire:model.live="doctorId" class="form-control @error('doctorId') border-red-500 @enderror" {{ empty($doctors) ? 'disabled' : '' }}>
                    <option value="">{{ empty($doctors) ? __('Select a hospital first') : __('Select a doctor') }}</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor['id'] }}">{{ $doctor['name'] }} - {{ $doctor['specialization'] ?? __('General') }}</option>
                    @endforeach
                </select>
                @if (!empty($validationErrors['doctorId']))
                    <p class="mt-1 text-sm text-red-600">{{ $validationErrors['doctorId'] }}</p>
                @endif
            </div>

            <!-- Doctor Cost Display -->
            @if ($selectedDoctor)
                <div class="alert alert-info">
                    <div class="flex items-start">
                        <iconify-icon icon="lucide:info" class="h-5 w-5 mt-0.5"></iconify-icon>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium">{{ $selectedDoctor->name }}</h4>
                            <p class="text-sm mt-1">{{ $selectedDoctor->specialization ?? __('General Practitioner') }}</p>
                            <div class="mt-2 space-y-1">
                                <div class="text-sm">
                                    <span class="font-medium">{{ __('Appointment Cost:') }}</span> ${{ number_format((float)($selectedDoctor->appointment_cost ?? 0), 2) }}
                                </div>
                                @if ($selectedDoctor->profit > 0)
                                    <div class="text-sm">
                                        <span class="font-medium">{{ __('Profit:') }}</span> ${{ number_format((float)$selectedDoctor->profit, 2) }}
                                    </div>
                                @endif
                                @if ($selectedDoctor->total > 0)
                                    <div class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                        <span class="font-bold">{{ __('Total:') }}</span> ${{ number_format((float)$selectedDoctor->total, 2) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Appointment DateTime - Single Field -->
            <div>
                <x-inputs.datetime-picker
                    name="appointmentDateTime"
                    label="{{ __('Appointment Date & Time') }}"
                    wire:model="appointmentDateTime"
                    :value="$appointmentDateTime"
                    :minDate="now()->format('Y-m-d')"
                    placeholder="{{ __('Select date and time') }}"
                    required
                />
                @if (!empty($validationErrors['appointmentDateTime']))
                    <p class="mt-1 text-sm text-red-600">{{ $validationErrors['appointmentDateTime'] }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Step 2: Customer Information -->
    <div x-show="currentStepNum === 2" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Customer Information') }}</h3>

        <div class="space-y-6">
            <!-- Customer Search -->
            <div>
                <label class="form-label">{{ __('Search Customer') }}</label>
                <input type="text" wire:model.live.debounce.300ms="customerSearch" class="form-control" placeholder="{{ __('Search by name or phone number') }}">
            </div>

            <!-- Matching Customers -->
            @if (!empty($matchingCustomers))
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg divide-y dark:divide-gray-700">
                    @foreach ($matchingCustomers as $customer)
                        <button type="button" wire:click="selectCustomer({{ $customer['id'] }})" class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer['phone'] }}</div>
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Selected Customer Display -->
            @if ($customerId && !$showNewCustomerForm)
                <div class="alert alert-success">
                    <div class="flex items-start">
                        <iconify-icon icon="lucide:check-circle" class="h-5 w-5 mt-0.5"></iconify-icon>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium">{{ __('Customer Selected') }}</h4>
                            <p class="text-sm mt-1">{{ $customerName }}</p>
                            <p class="text-sm">{{ $customerPhone }}</p>
                        </div>
                    </div>
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

            @if (!empty($validationErrors['customerName']))
                <p class="text-sm text-red-600">{{ $validationErrors['customerName'] }}</p>
            @endif
        </div>
    </div>

    <!-- Step 3: Review & Confirm -->
    <div x-show="currentStepNum === 3" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('Review & Confirm') }}</h3>

        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 space-y-4">
            <!-- Appointment Details -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Appointment Details') }}</h4>
                <dl class="grid grid-cols-1 gap-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Type:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $appointmentType === 'self' ? __('Self') : __('Someone Else') }}</dd>
                    </div>
                    @if ($appointmentType === 'someone_else')
                        <div class="flex justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">{{ __('Patient Name:') }}</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $patientName }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Hospital:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ collect($hospitals)->firstWhere('id', $hospitalId)['name'] ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Doctor:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $selectedDoctor->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Date & Time:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ !empty($appointmentDateTime) ? date('M d, Y \a\t h:i A', strtotime($appointmentDateTime)) : '-' }}</dd>
                    </div>
                    @if ($selectedDoctor && $selectedDoctor->total > 0)
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-gray-900 dark:text-white font-semibold">{{ __('Total Cost:') }}</dt>
                            <dd class="font-bold text-primary-600 dark:text-primary-400">${{ number_format((float)$selectedDoctor->total, 2) }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Customer Details -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Customer Information') }}</h4>
                <dl class="grid grid-cols-1 gap-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Name:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $customerName }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Phone:') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $customerPhone }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
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
            <button type="button" x-show="currentStepNum === 3" wire:click="submitAppointment" wire:loading.attr="disabled" wire:target="submitAppointment" class="btn btn-success">
                <iconify-icon icon="lucide:credit-card" class="mr-2 h-4 w-4"></iconify-icon>
                <span wire:loading.remove wire:target="submitAppointment">
                    @if ($selectedDoctor && $selectedDoctor->total > 0)
                        {{ __('Pay') }} ${{ number_format((float)$selectedDoctor->total, 2) }}
                    @else
                        {{ __('Pay Now') }}
                    @endif
                </span>
                <span wire:loading wire:target="submitAppointment">
                    {{ __('Processing...') }}
                </span>
            </button>
        </div>
    </div>

    <!-- Payment Modal using wire:loading -->
    <div wire:loading.delay wire:target="submitAppointment" 
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
         style="margin: 0; padding: 1rem; left: 0; right: 0; top: 0; bottom: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-8 w-full max-w-md mx-auto">
            <div class="text-center">
                <!-- Payment Progress Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4 bg-blue-100 dark:bg-blue-900">
                    <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Payment Status Message -->
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('Processing Payment') }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('Please wait while we process your payment...') }}
                </p>

                <!-- Progress Steps -->
                <div class="flex justify-center space-x-2 mb-4">
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></div>
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="h-2 w-2 rounded-full bg-blue-600 animate-pulse" style="animation-delay: 0.4s"></div>
                </div>

                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ __('Please do not close this window') }}
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function appointmentBooking() {
            return {
                currentStepNum: @entangle('currentStep'),
                steps: [
                    { label: '{{ __("Appointment") }}', description: '{{ __("Details") }}' },
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
