<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Statistics Cards -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Total Providers -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:building" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Providers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_providers']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Active Providers -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Active Providers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['active_providers']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Inactive Providers -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-red-500 text-white">
                            <iconify-icon icon="lucide:x-circle" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Inactive Providers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['inactive_providers']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Providers Datatable -->
    <x-card class="bg-white dark:bg-gray-800">
        <livewire:datatable.provider-datatable lazy />
    </x-card>

    <x-import-modal
        title="{{ __('Import Providers') }}"
        :instructions="['Download the sample template', 'Fill in provider data', 'Name and Email must be unique', 'Upload CSV file']"
        :sampleTemplateUrl="route('admin.providers.sample-template')"
        :importUrl="route('admin.providers.import')"
        :requiredFields="['Name: Required, unique', 'Email: Required, unique', 'Phone: Required', 'Status: Required (active/inactive)']"
    />
</x-layouts.backend-layout>
