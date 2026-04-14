<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use App\Services\PdfExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller
{
    public function __construct(
        private readonly FinancialReportService $reportService,
        private readonly PdfExportService $pdfExportService
    ) {
    }

    public function index(): View
    {
        $summary = $this->reportService->getDashboardSummary();

        return view('backend.pages.reports.index', [
            'summary' => $summary,
            'breadcrumbs' => [
                'title' => __('Financial Reports'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Financial Reports'), 'url' => null],
                ],
            ],
        ]);
    }

    public function revenue(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getRevenueReport($startDate, $endDate);

        return view('backend.pages.reports.revenue', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'breadcrumbs' => [
                'title' => __('Revenue Report'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Financial Reports'), 'url' => route('admin.reports.index')],
                    ['label' => __('Revenue'), 'url' => null],
                ],
            ],
        ]);
    }

    public function expenses(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getExpenseReport($startDate, $endDate);

        return view('backend.pages.reports.expenses', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'breadcrumbs' => [
                'title' => __('Expense Report'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Financial Reports'), 'url' => route('admin.reports.index')],
                    ['label' => __('Expenses'), 'url' => null],
                ],
            ],
        ]);
    }

    public function providerPayouts(Request $request): View
    {
        return view('backend.pages.reports.provider-payouts', [
            'breadcrumbs' => [
                'title' => __('Provider Payout Management'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Financial Reports'), 'url' => route('admin.reports.index')],
                    ['label' => __('Provider Payouts'), 'url' => null],
                ],
            ],
        ]);
    }

    public function profitLoss(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getProfitLossReport($startDate, $endDate);

        return view('backend.pages.reports.profit-loss', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'breadcrumbs' => [
                'title' => __('Profit & Loss Report'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Financial Reports'), 'url' => route('admin.reports.index')],
                    ['label' => __('Profit & Loss'), 'url' => null],
                ],
            ],
        ]);
    }

    public function exportRevenuePdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getRevenueReport($startDate, $endDate);

        $pdf = $this->pdfExportService->generateFromView('backend.pages.reports.pdf.revenue', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download("revenue-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function exportExpensesPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getExpenseReport($startDate, $endDate);

        $pdf = $this->pdfExportService->generateFromView('backend.pages.reports.pdf.expenses', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download("expense-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function exportProfitLossPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $report = $this->reportService->getProfitLossReport($startDate, $endDate);

        $pdf = $this->pdfExportService->generateFromView('backend.pages.reports.pdf.profit-loss', [
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download("profit-loss-report-{$startDate}-to-{$endDate}.pdf");
    }
}
