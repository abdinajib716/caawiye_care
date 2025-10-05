<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Orders') }}
            </h2>
            <div class="flex items-center gap-3">
                <nav>
                    <ol class="flex items-center gap-1.5 pe-2">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                                {{ __("Home") }}
                                <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                                {{ __("Dashboard") }}
                                <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                            </a>
                        </li>
                        <li class="text-sm text-gray-700 dark:text-white/90">
                            {{ __('Orders') }}
                        </li>
                    </ol>
                </nav>
                <a
                    href="{{ route('admin.order-zone.index') }}"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
                >
                    <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('New Order') }}
                </a>
            </div>
        </div>
        <x-messages />
    </x-slot>

    <div class="space-y-6">

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Total Orders -->
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:shopping-cart" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Total Orders') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format((int) $statistics['total_orders']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Pending Orders -->
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500 text-white">
                            <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Pending') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format((int) $statistics['pending_orders']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Processing Orders -->
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-orange-500 text-white">
                            <iconify-icon icon="lucide:loader-2" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Processing') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format((int) $statistics['processing_orders']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Completed Orders -->
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Completed') }}</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format((int) $statistics['completed_orders']) }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Total Revenue -->
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                            <iconify-icon icon="lucide:dollar-sign" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Total Revenue') }}</div>
                        <div class="text-2xl font-bold text-gray-900">${{ number_format((float) $statistics['total_revenue'], 2) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Orders Datatable -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @livewire('datatable.order-datatable')
        </div>
    </div>
</x-layouts.backend-layout>

