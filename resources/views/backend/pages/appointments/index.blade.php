<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Total Appointments -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:calendar" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Appointments') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_appointments']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Scheduled Appointments -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500 text-white">
                            <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Scheduled') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['scheduled_appointments']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Confirmed Appointments -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Confirmed') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['confirmed_appointments']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Completed Appointments -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                            <iconify-icon icon="lucide:check-check" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['completed_appointments']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Cancelled Appointments -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white">
                            <iconify-icon icon="lucide:x-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cancelled') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['cancelled_appointments']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Datatable -->
        <x-card class="bg-white dark:bg-gray-800">
            <livewire:datatable.appointment-datatable lazy />
        </x-card>
    </div>
</x-layouts.backend-layout>

