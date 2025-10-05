<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:grid-3x3" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Total Services') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_services']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Active Services') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($statistics['active_services']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                            <iconify-icon icon="lucide:dollar-sign" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Average Price') }}</div>
                        <div class="text-2xl font-bold text-gray-900">${{ number_format($statistics['average_price'], 2) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Services Management -->
        <x-card class="bg-white">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Services Management') }}</h3>
            </x-slot>

            <!-- Services Datatable -->
            <livewire:datatable.service-datatable />
        </x-card>
    </div>

</x-layouts.backend-layout>
