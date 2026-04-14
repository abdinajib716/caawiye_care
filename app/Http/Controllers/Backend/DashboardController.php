<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Charts\DashboardChartService;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly DashboardChartService $dashboardChartService
    ) {
    }

    public function index()
    {
        $this->authorize('viewDashboard', User::class);

        $metrics = $this->dashboardService->getMetrics();

        return view(
            'backend.pages.dashboard.index',
            [
                'primaryKpis' => [
                    [
                        'label' => __('Customers'),
                        'value' => number_format($metrics['customers_total']),
                        'icon' => 'lucide:users',
                        'color' => 'bg-sky-500',
                        'url' => route('admin.customers.index'),
                    ],
                    [
                        'label' => __('Active Appointments'),
                        'value' => number_format($metrics['appointments_active']),
                        'icon' => 'lucide:calendar-heart',
                        'color' => 'bg-indigo-500',
                        'url' => route('admin.appointments.index'),
                    ],
                    [
                        'label' => __('Active Diagnostics'),
                        'value' => number_format($metrics['diagnostics_active']),
                        'icon' => 'lucide:activity',
                        'color' => 'bg-amber-500',
                        'url' => null,
                    ],
                    [
                        'label' => __('Pending Medicine Delivery'),
                        'value' => number_format($metrics['medicine_pending_delivery']),
                        'icon' => 'lucide:pill',
                        'color' => 'bg-emerald-500',
                        'url' => route('admin.medicine-orders.index'),
                    ],
                    [
                        'label' => __('Open Refunds'),
                        'value' => number_format($metrics['refunds_open']),
                        'icon' => 'lucide:rotate-ccw',
                        'color' => 'bg-rose-500',
                        'url' => route('admin.refunds.index'),
                    ],
                    [
                        'label' => __('Net Verified Revenue'),
                        'value' => '$' . number_format((float) $metrics['net_verified_revenue'], 2),
                        'icon' => 'lucide:wallet',
                        'color' => 'bg-teal-500',
                        'url' => route('admin.reports.profit-loss'),
                    ],
                ],
                'revenue_chart_data' => $this->dashboardChartService->getRevenueTrendData(
                    request()->get('chart_filter_period', 'last_6_months')
                ),
                'workload_chart_data' => $this->dashboardChartService->getWorkloadBreakdownData(),
                'breadcrumbs' => [
                    'title' => __('Dashboard'),
                    'show_home' => false,
                    'show_current' => false,
                ],
            ]
        );
    }
}
