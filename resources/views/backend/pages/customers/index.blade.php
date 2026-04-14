<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:users" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Customers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_customers']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Active Customers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['active_customers']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white">
                            <iconify-icon icon="lucide:user-x" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Inactive Customers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['inactive_customers']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Customers Management -->
        <x-card class="bg-white dark:bg-gray-800">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Customers Management') }}</h3>
            </x-slot>

            <!-- Customers Datatable -->
            <livewire:datatable.customer-datatable lazy />
        </x-card>
    </div>

    {{-- Import Modal --}}
    <x-import-modal
        title="{{ __('Import Customers') }}"
        :instructions="[
            __('Download the sample template below'),
            __('Fill in your customer data following the format'),
            __('Ensure all required fields are completed'),
            __('Email must be unique (or leave empty)'),
            __('Status must be either active or inactive'),
            __('Upload the completed CSV file'),
        ]"
        :sampleTemplateUrl="route('admin.customers.sample-template')"
        :importUrl="route('admin.customers.import')"
        :requiredFields="[
            __('Name: Required, max 255 characters'),
            __('Email: Optional, must be valid email'),
            __('Phone: Required, max 20 characters'),
            __('Country Code: Required (e.g., +252)'),
            __('Address: Optional'),
            __('Status: Required (active or inactive)'),
        ]"
    />

</x-layouts.backend-layout>
