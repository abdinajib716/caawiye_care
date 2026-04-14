<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    @section('before_vite_build')
        <script>
            var dashboardBarChartData = @json($revenue_chart_data['data']);
            var dashboardBarChartLabels = @json($revenue_chart_data['labels']);
            var dashboardBarChartSeriesName = @json(__('Net Revenue'));
        </script>
    @endsection

    {!! Hook::applyFilters(DashboardFilterHook::DASHBOARD_AFTER_BREADCRUMBS, '') !!}

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($primaryKpis as $kpi)
            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 {{ !empty($kpi['url']) ? 'cursor-pointer' : '' }}"
                @if(!empty($kpi['url']))
                    onclick="window.location.href='{{ $kpi['url'] }}'"
                @endif
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $kpi['label'] }}</div>
                        <div class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $kpi['value'] }}</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $kpi['color'] }}">
                        <iconify-icon icon="{{ $kpi['icon'] }}" class="h-6 w-6 text-white"></iconify-icon>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {!! Hook::applyFilters(DashboardFilterHook::DASHBOARD_CARDS_AFTER, '') !!}

    <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 md:col-span-8">
            @include('backend.pages.dashboard.partials.user-growth')
        </div>
        <div class="col-span-12 md:col-span-4">
            @include('backend.pages.dashboard.partials.user-history')
        </div>
    </div>

    {!! Hook::applyFilters(DashboardFilterHook::DASHBOARD_AFTER, '') !!}
</x-layouts.backend-layout>
