@php $currentFilter = request()->get('chart_filter_period', 'last_6_months'); @endphp

<div class="rounded-md shadow-sm border border-gray-200 dark:border-gray-700 p-4 py-6 z-1 bg-white dark:bg-gray-800">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-white">
            {{ __('Revenue Trend') }}
        </h3>
        <div class="flex gap-2 items-center">
            <span
                class="bg-indigo-100 text-indigo-900 px-4 py-2 rounded-full text-sm">
                {{ __(ucfirst(str_replace('_', ' ', $currentFilter))) }}
            </span>

            <!-- Alpine Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="btn-primary flex items-center gap-2">
                    <iconify-icon icon="lucide:sliders"></iconify-icon>
                    {{ __('Filter') }}
                    <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-44 rounded-md shadow-sm bg-white dark:bg-gray-700 z-10">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=last_6_months"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'last_6_months' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('Last 6 months') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=last_12_months"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'last_12_months' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('Last 12 months') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=this_year"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'this_year' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('This year') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=last_year"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'last_year' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('Last year') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=last_30_days"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'last_30_days' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('Last 30 days') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=last_7_days"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'last_7_days' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('Last 7 days') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}?chart_filter_period=this_month"
                               class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $currentFilter === 'this_month' ? 'bg-blue-100 dark:bg-gray-600' : '' }}">
                                <span class="ml-2"> {{ __('This month') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="h-60" id="user-growth-chart"></div>
</div>
