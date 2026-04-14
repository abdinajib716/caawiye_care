<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Statistics Cards -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <!-- Total Services -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:scan" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Services') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_services']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Total Providers -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:building-2" class="h-6 w-6"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Active Providers') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int)$statistics['active_providers']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Services Datatable -->
    <x-card class="bg-white dark:bg-gray-800">
        <livewire:datatable.scan-imaging-service-datatable lazy />
    </x-card>

    <x-import-modal
        title="{{ __('Import Scan/Imaging Services') }}"
        :instructions="['Download the sample template', 'Fill in service data', 'Provider must exist', 'Cost must be numeric', 'Upload CSV file']"
        :sampleTemplateUrl="route('admin.scan-imaging-services.sample-template')"
        :importUrl="route('admin.scan-imaging-services.import')"
        :requiredFields="['Service Name: Required', 'Cost: Required, numeric', 'Provider: Required (must exist)', 'Status: Required (active/inactive)']"
    />
</x-layouts.backend-layout>
