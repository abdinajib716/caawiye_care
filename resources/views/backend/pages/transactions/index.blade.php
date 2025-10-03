<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <!-- Statistics Cards -->
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Transactions -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Transactions') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format((int) $statistics['total_transactions']) }}
                    </p>
                </div>
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                    <iconify-icon icon="lucide:credit-card" class="h-6 w-6 text-blue-600 dark:text-blue-300"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Successful Transactions -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Successful') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format((int) $statistics['successful_transactions']) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        ${{ number_format((float) $statistics['total_amount'], 2) }}
                    </p>
                </div>
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                    <iconify-icon icon="lucide:check-circle" class="h-6 w-6 text-green-600 dark:text-green-300"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Failed Transactions -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Failed') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format((int) $statistics['failed_transactions']) }}
                    </p>
                </div>
                <div class="rounded-full bg-red-100 p-3 dark:bg-red-900">
                    <iconify-icon icon="lucide:x-circle" class="h-6 w-6 text-red-600 dark:text-red-300"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Pending') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format((int) $statistics['pending_transactions']) }}
                    </p>
                </div>
                <div class="rounded-full bg-yellow-100 p-3 dark:bg-yellow-900">
                    <iconify-icon icon="lucide:clock" class="h-6 w-6 text-yellow-600 dark:text-yellow-300"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Datatable -->
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <livewire:datatable.payment-transaction-datatable />
    </div>
</x-layouts.backend-layout>

