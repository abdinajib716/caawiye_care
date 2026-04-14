<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Refunds') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500 text-white">
                            <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['pending_count']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-orange-500 text-white">
                            <iconify-icon icon="lucide:loader-2" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Processing') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['processing_count']) }}</div>
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
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['completed_count']) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                            <iconify-icon icon="lucide:dollar-sign" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Refunded') }}</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($statistics['total_refunded_amount'], 2) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Refunds Datatable -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <livewire:datatable.refund-datatable lazy />
        </div>
    </div>
</x-layouts.backend-layout>
