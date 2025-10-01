<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Stepper -->
        <x-stepper
            :steps="[
                ['label' => __('Services'), 'description' => __('Select services')],
                ['label' => __('Customer'), 'description' => __('Customer info')],
                ['label' => __('Payment'), 'description' => __('Review & pay')],
            ]"
            :current-step="1"
        />

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Services Selection (2/3 width) -->
            <div class="lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.service-selection')
                </div>
            </div>

            <!-- Right Column: Customer & Preview (1/3 width) -->
            <div class="space-y-6">
                <!-- Customer Lookup -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    @livewire('order-zone.customer-lookup')
                </div>

                <!-- Order Preview -->
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
        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-error', (event) => {
                // Show error notification
                if (typeof window.showToast === 'function') {
                    window.showToast(event.message, 'error');
                } else {
                    alert(event.message);
                }
            });
        });
    </script>
    @endpush
</x-layouts.backend-layout>

