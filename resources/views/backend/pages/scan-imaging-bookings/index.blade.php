<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Statistics Cards -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <!-- Total Bookings -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:scan" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_bookings']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Pending -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-yellow-500 text-white">
                            <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['pending_bookings']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Confirmed -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Confirmed') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['confirmed_bookings']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- In Progress -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-orange-500 text-white">
                            <iconify-icon icon="lucide:loader" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('In Progress') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['in_progress_bookings']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Completed -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-check" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['completed_bookings']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Cancelled -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-red-500 text-white">
                            <iconify-icon icon="lucide:x-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Cancelled') }}</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($statistics['cancelled_bookings']) }}</div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Scan & Imaging Bookings Datatable -->
    <x-card class="bg-white dark:bg-gray-800">
        <livewire:datatable.scan-imaging-booking-datatable lazy />
    </x-card>
</x-layouts.backend-layout>
