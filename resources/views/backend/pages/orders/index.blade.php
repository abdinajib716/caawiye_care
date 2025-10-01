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
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Total Orders -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Orders') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format((int) $statistics['total_orders']) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                        <iconify-icon icon="lucide:shopping-cart" class="h-6 w-6 text-blue-600 dark:text-blue-400"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Pending') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format((int) $statistics['pending_orders']) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-yellow-100 p-3 dark:bg-yellow-900/30">
                        <iconify-icon icon="lucide:clock" class="h-6 w-6 text-yellow-600 dark:text-yellow-400"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Processing') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format((int) $statistics['processing_orders']) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-orange-100 p-3 dark:bg-orange-900/30">
                        <iconify-icon icon="lucide:loader-2" class="h-6 w-6 text-orange-600 dark:text-orange-400"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Completed') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format((int) $statistics['completed_orders']) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                        <iconify-icon icon="lucide:check-circle" class="h-6 w-6 text-green-600 dark:text-green-400"></iconify-icon>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Revenue') }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format((float) $statistics['total_revenue'], 2) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
                        <iconify-icon icon="lucide:dollar-sign" class="h-6 w-6 text-purple-600 dark:text-purple-400"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Datatable -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @livewire('datatable.order-datatable')
        </div>
    </div>
</x-layouts.backend-layout>

