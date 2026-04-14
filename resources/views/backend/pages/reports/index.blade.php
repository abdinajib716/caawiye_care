<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Financial Reports') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="space-y-6">
        <!-- Today's Summary -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-card class="bg-gradient-to-br from-green-500 to-green-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">{{ __("Today's Revenue") }}</p>
                        <p class="mt-1 text-3xl font-bold">${{ number_format($summary['today']['revenue'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-white/20 p-3">
                        <iconify-icon icon="lucide:trending-up" class="h-8 w-8"></iconify-icon>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-red-500 to-red-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">{{ __("Today's Expenses") }}</p>
                        <p class="mt-1 text-3xl font-bold">${{ number_format($summary['today']['expenses'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-white/20 p-3">
                        <iconify-icon icon="lucide:trending-down" class="h-8 w-8"></iconify-icon>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">{{ __("Today's Profit") }}</p>
                        <p class="mt-1 text-3xl font-bold">${{ number_format($summary['today']['profit'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-white/20 p-3">
                        <iconify-icon icon="lucide:wallet" class="h-8 w-8"></iconify-icon>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Monthly Summary -->
        <x-card class="bg-white dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('This Month') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Revenue') }}</p>
                    <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($summary['month']['revenue'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Expenses') }}</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($summary['month']['expenses'], 2) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Net Profit') }}</p>
                    <p class="mt-1 text-2xl font-bold {{ $summary['month']['profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">${{ number_format($summary['month']['profit'], 2) }}</p>
                </div>
            </div>
        </x-card>

        <!-- Pending Actions -->
        <x-card class="bg-white dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Pending Actions') }}</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <a href="{{ route('admin.refunds.index') }}?status=pending" class="flex items-center gap-3 rounded-lg border border-yellow-200 bg-yellow-50 p-4 hover:bg-yellow-100 dark:border-yellow-800 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/30">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-500 text-white">
                        <iconify-icon icon="lucide:undo-2" class="h-5 w-5"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-yellow-800 dark:text-yellow-400">{{ $summary['pending']['refunds'] }}</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-500">{{ __('Pending Refunds') }}</p>
                    </div>
                </a>
                <a href="{{ route('admin.expenses.index') }}?status=pending_approval" class="flex items-center gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 hover:bg-orange-100 dark:border-orange-800 dark:bg-orange-900/20 dark:hover:bg-orange-900/30">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-white">
                        <iconify-icon icon="lucide:receipt" class="h-5 w-5"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-orange-800 dark:text-orange-400">{{ $summary['pending']['expense_approvals'] }}</p>
                        <p class="text-sm text-orange-600 dark:text-orange-500">{{ __('Expense Approvals') }}</p>
                    </div>
                </a>
                <div class="flex items-center gap-3 rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-500 text-white">
                        <iconify-icon icon="lucide:users" class="h-5 w-5"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-800 dark:text-purple-400">{{ $summary['pending']['provider_payments'] }}</p>
                        <p class="text-sm text-purple-600 dark:text-purple-500">{{ __('Provider Payments') }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Report Links -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.reports.revenue') }}" class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-500 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-green-500">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                    <iconify-icon icon="lucide:trending-up" class="h-6 w-6"></iconify-icon>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Revenue Report') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('View revenue by date and service type') }}</p>
            </a>

            <a href="{{ route('admin.reports.expenses') }}" class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-red-500 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-red-500">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    <iconify-icon icon="lucide:trending-down" class="h-6 w-6"></iconify-icon>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Expense Report') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('View expenses by category and date') }}</p>
            </a>

            <a href="{{ route('admin.reports.provider-payouts') }}" class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-purple-500 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-purple-500">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <iconify-icon icon="lucide:users" class="h-6 w-6"></iconify-icon>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Provider Payouts') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Track provider payments and balances') }}</p>
            </a>

            <a href="{{ route('admin.reports.profit-loss') }}" class="group rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-500 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-500">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <iconify-icon icon="lucide:file-text" class="h-6 w-6"></iconify-icon>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Profit & Loss') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Complete P&L statement') }}</p>
            </a>
        </div>
    </div>
</x-layouts.backend-layout>
