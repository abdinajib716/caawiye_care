<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6" x-data="orderZoneManager()">
        <!-- Stepper -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-1 items-center">
                        <div class="flex items-center">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-colors"
                                :class="currentStep > index + 1 ? 'border-green-500 bg-green-500 text-white' : currentStep === index + 1 ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300 bg-white text-gray-500 dark:border-gray-600 dark:bg-gray-700'"
                            >
                                <template x-if="currentStep > index + 1">
                                    <iconify-icon icon="lucide:check" class="h-5 w-5"></iconify-icon>
                                </template>
                                <template x-if="currentStep <= index + 1">
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
                            :class="currentStep > index + 1 ? 'bg-green-500' : ''"
                        ></div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Step Content (2/3 width) -->
            <div class="lg:col-span-2">
                <!-- Step 1: Service Selection -->
                <div x-show="currentStep === 1" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.service-selection')
                </div>

                <!-- Step 2: Service Details (conditional) -->
                <div x-show="currentStep === 2 && hasCustomFieldServices" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.service-details-step')
                </div>

                <!-- Step 3: Customer Lookup -->
                <div x-show="currentStep === (hasCustomFieldServices ? 3 : 2)" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.customer-lookup')
                </div>
            </div>

            <!-- Right Column: Order Preview (1/3 width) -->
            <div class="space-y-6">
                <!-- Order Preview - Always visible -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.order-preview')
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-start">
                <iconify-icon icon="lucide:info" class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400"></iconify-icon>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('How to use Order Zone') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ol class="list-decimal space-y-1 pl-5">
                            <li>{{ __('Select services from the list and adjust quantities') }}</li>
                            <li>{{ __('Fill in service details if required (e.g., appointment information)') }}</li>
                            <li>{{ __('Enter customer phone number to lookup or create new customer') }}</li>
                            <li>{{ __('Review order details and click "Process Order"') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function orderZoneManager() {
            return {
                currentStep: 1,
                hasCustomFieldServices: false,
                steps: [
                    { label: '{{ __("Services") }}', description: '{{ __("Select services") }}' },
                    { label: '{{ __("Details") }}', description: '{{ __("Service details") }}' },
                    { label: '{{ __("Customer") }}', description: '{{ __("Customer info") }}' },
                    { label: '{{ __("Payment") }}', description: '{{ __("Review & pay") }}' },
                ],

                init() {
                    // Listen for services updated to check for custom fields
                    Livewire.on('services-updated', (data) => {
                        this.checkForCustomFields(data[0].services || []);
                        this.updateSteps();
                    });

                    // Listen for service details completed
                    Livewire.on('service-details-completed', () => {
                        this.goToStep(this.hasCustomFieldServices ? 3 : 2);
                    });

                    // Listen for customer updated
                    Livewire.on('customer-updated', () => {
                        this.goToStep(this.hasCustomFieldServices ? 4 : 3);
                    });

                    // Listen for clear order
                    Livewire.on('clear-order', () => {
                        this.currentStep = 1;
                        this.hasCustomFieldServices = false;
                        this.updateSteps();
                    });

                    // Listen for custom go-to-step event
                    window.addEventListener('go-to-step', (event) => {
                        this.goToStep(event.detail.step);
                    });
                },

                checkForCustomFields(services) {
                    this.hasCustomFieldServices = services.some(service => {
                        // Check if service has custom fields (you can add more logic here)
                        return service.has_custom_fields || false;
                    });
                },

                updateSteps() {
                    if (this.hasCustomFieldServices) {
                        this.steps = [
                            { label: '{{ __("Services") }}', description: '{{ __("Select services") }}' },
                            { label: '{{ __("Details") }}', description: '{{ __("Service details") }}' },
                            { label: '{{ __("Customer") }}', description: '{{ __("Customer info") }}' },
                            { label: '{{ __("Payment") }}', description: '{{ __("Review & pay") }}' },
                        ];
                    } else {
                        this.steps = [
                            { label: '{{ __("Services") }}', description: '{{ __("Select services") }}' },
                            { label: '{{ __("Customer") }}', description: '{{ __("Customer info") }}' },
                            { label: '{{ __("Payment") }}', description: '{{ __("Review & pay") }}' },
                        ];
                    }
                },

                goToStep(step) {
                    this.currentStep = step;
                }
            }
        }

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-error', (event) => {
                // Show error notification
                if (typeof window.showToast === 'function') {
                    window.showToast(event[0].message, 'error');
                } else {
                    alert(event[0].message);
                }
            });
        });
    </script>
    @endpush
</x-layouts.backend-layout>

