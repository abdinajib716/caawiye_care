<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Statistics Cards -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Total Lab Tests -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:flask-conical" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Lab Tests') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_tests']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Bill Provider -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-orange-500 text-white">
                            <iconify-icon icon="lucide:building-2" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bill Provider') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int)$statistics['bill_provider_tests']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Bill Customer -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:users" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bill Customer') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int)$statistics['bill_customer_tests']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Lab Tests Datatable -->
    <x-card class="bg-white dark:bg-gray-800">
        <livewire:datatable.lab-test-datatable lazy />
    </x-card>

    <x-import-modal
        title="{{ __('Import Lab Tests') }}"
        :instructions="['Download the sample template', 'Fill in lab test data', 'Provider must exist', 'Cost must be numeric', 'Upload CSV file']"
        :sampleTemplateUrl="route('admin.lab-tests.sample-template')"
        :importUrl="route('admin.lab-tests.import')"
        :requiredFields="['Name: Required', 'Cost: Required, numeric', 'Provider: Required (must exist)', 'Status: Required (active/inactive)']"
    />
</x-layouts.backend-layout>
