<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <!-- Statistics Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <!-- Total Orders -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <iconify-icon icon="lucide:package" class="h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Orders') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total_orders']) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                    <iconify-icon icon="lucide:clock" class="h-5 w-5 text-yellow-600 dark:text-yellow-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['pending_orders']) }}</p>
                </div>
            </div>
        </div>

        <!-- In Office -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <iconify-icon icon="lucide:building-2" class="h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('In Office') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['in_office_orders']) }}</p>
                </div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <iconify-icon icon="lucide:truck" class="h-5 w-5 text-green-600 dark:text-green-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Delivered') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['delivered_orders']) }}</p>
                </div>
            </div>
        </div>

        <!-- Cancelled -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <iconify-icon icon="lucide:x-circle" class="h-5 w-5 text-red-600 dark:text-red-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cancelled') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['cancelled_orders']) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <iconify-icon icon="lucide:dollar-sign" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Revenue') }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($statistics['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    @livewire('datatable.medicine-order-datatable')
</x-layouts.backend-layout>
