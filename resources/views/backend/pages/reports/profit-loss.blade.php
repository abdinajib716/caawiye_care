<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Profit & Loss Report') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="space-y-6">
        <!-- Date Filter -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6">
                <form action="{{ route('admin.reports.profit-loss') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="w-48">
                        <x-inputs.date-picker 
                            name="start_date" 
                            label="{{ __('Start Date') }}" 
                            :value="$startDate" 
                        />
                    </div>
                    <div class="w-48">
                        <x-inputs.date-picker 
                            name="end_date" 
                            label="{{ __('End Date') }}" 
                            :value="$endDate" 
                        />
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="lucide:filter" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Apply Filter') }}
                    </button>
                    <a href="{{ route('admin.reports.export-profit-loss-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary">
                        <iconify-icon icon="lucide:download" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Export PDF') }}
                    </a>
                </form>
            </div>
        </x-card>

        <!-- P&L Statement -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="mb-6 border-b border-gray-200 pb-4 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Profit & Loss Statement') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Period:') }} {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>

            <div class="space-y-6">
                <!-- Revenue Section -->
                <div>
                    <h4 class="mb-3 text-lg font-semibold text-green-600 dark:text-green-400">{{ __('REVENUE') }}</h4>
                    <div class="space-y-2 pl-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Gross Revenue') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">${{ number_format($report['gross_revenue'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Less: Refunded Revenue') }}</span>
                            <span class="font-medium text-red-600 dark:text-red-400">(${{ number_format($report['refunded_revenue'], 2) }})</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 text-sm dark:border-gray-700">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ __('Net Revenue') }}</span>
                            <span class="font-bold text-green-600 dark:text-green-400">${{ number_format($report['net_revenue'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Expenses Section -->
                <div>
                    <h4 class="mb-3 text-lg font-semibold text-red-600 dark:text-red-400">{{ __('EXPENSES') }}</h4>
                    <div class="space-y-2 pl-4">
                        @forelse($report['expenses_by_category'] as $category => $amount)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">{{ $category }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('No expenses recorded') }}</div>
                        @endforelse
                        <div class="flex justify-between border-t border-gray-200 pt-2 text-sm dark:border-gray-700">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ __('Total Expenses') }}</span>
                            <span class="font-bold text-red-600 dark:text-red-400">${{ number_format($report['total_expenses'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Net Profit -->
                <div class="rounded-lg border-2 {{ $report['net_profit'] >= 0 ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }} p-4">
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('NET PROFIT / (LOSS)') }}</span>
                        <span class="text-2xl font-bold {{ $report['net_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $report['net_profit'] >= 0 ? '' : '(' }}${{ number_format(abs($report['net_profit']), 2) }}{{ $report['net_profit'] >= 0 ? '' : ')' }}
                        </span>
                    </div>
                    @if($report['profit_margin'] != 0)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Profit Margin:') }} {{ number_format($report['profit_margin'], 1) }}%
                        </p>
                    @endif
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.backend-layout>
