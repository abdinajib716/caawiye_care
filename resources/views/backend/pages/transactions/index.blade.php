<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <!-- Statistics Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Transactions -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                        <iconify-icon icon="lucide:credit-card" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Transactions') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $statistics['total_transactions']) }}</div>
                </div>
            </div>
        </x-card>

        <!-- Successful Transactions -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                        <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Successful') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $statistics['successful_transactions']) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${{ number_format((float) $statistics['total_amount'], 2) }}</div>
                </div>
            </div>
        </x-card>

        <!-- Failed Transactions -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white">
                        <iconify-icon icon="lucide:x-circle" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Failed') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $statistics['failed_transactions']) }}</div>
                </div>
            </div>
        </x-card>

        <!-- Pending Transactions -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500 text-white">
                        <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format((int) $statistics['pending_transactions']) }}</div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Transactions Datatable -->
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <livewire:datatable.payment-transaction-datatable lazy />
    </div>
</x-layouts.backend-layout>

