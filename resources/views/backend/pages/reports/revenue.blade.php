<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Revenue Report') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="space-y-6">
        <!-- Date Filter -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6">
                <form action="{{ route('admin.reports.revenue') }}" method="GET" class="flex flex-wrap items-end gap-4">
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
                    <a href="{{ route('admin.reports.export-revenue-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary">
                        <iconify-icon icon="lucide:download" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Export PDF') }}
                    </a>
                </form>
            </div>
        </x-card>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:trending-up" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Gross Revenue') }}</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($report['gross_revenue'], 2) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-red-500 text-white">
                            <iconify-icon icon="lucide:undo-2" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Refunded') }}</div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($report['refunded_revenue'], 2) }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:wallet" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Net Revenue') }}</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${{ number_format($report['net_revenue'], 2) }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Revenue by Service Type -->
        <x-card class="bg-white dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Revenue by Service Type') }}</h3>
            @if(count($report['by_service_type']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Service Type') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Revenue') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('% of Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach($report['by_service_type'] as $type => $amount)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($amount, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ $report['net_revenue'] > 0 ? number_format(($amount / $report['gross_revenue']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">{{ __('No revenue data for this period') }}</p>
            @endif
        </x-card>

        <!-- Revenue by Date -->
        <x-card class="bg-white dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Revenue by Date') }}</h3>
            @if(count($report['by_date']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Date') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach($report['by_date'] as $date => $amount)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">{{ __('No revenue data for this period') }}</p>
            @endif
        </x-card>
    </div>
</x-layouts.backend-layout>
