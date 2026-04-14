<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RevenueLedger;
use App\Models\Expense;
use App\Models\Refund;
use App\Models\ProviderPayment;
use App\Models\Order;
use App\Models\LabTestBooking;
use App\Models\MedicineOrder;
use App\Models\ScanImagingBooking;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    public function __construct(
        private RevenueLedgerService $revenueLedgerService,
        private ExpenseService $expenseService
    ) {
    }

    public function getRevenueReport(?string $startDate = null, ?string $endDate = null): array
    {
        $grossRevenue = $this->revenueLedgerService->getGrossRevenue($startDate, $endDate);
        $refundedRevenue = $this->revenueLedgerService->getRefundedRevenue($startDate, $endDate);
        $netRevenue = $grossRevenue - $refundedRevenue;

        $revenueByServiceType = $this->revenueLedgerService->getRevenueByServiceType($startDate, $endDate);
        $revenueByDate = $this->revenueLedgerService->getRevenueByDate($startDate, $endDate);

        return [
            'gross_revenue' => $grossRevenue,
            'refunded_revenue' => $refundedRevenue,
            'net_revenue' => $netRevenue,
            'by_service_type' => $revenueByServiceType,
            'by_date' => $revenueByDate,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }

    public function getExpenseReport(?string $startDate = null, ?string $endDate = null): array
    {
        $totalExpenses = $this->expenseService->getTotalExpenses($startDate, $endDate);
        $expensesByCategory = $this->expenseService->getExpensesByCategory($startDate, $endDate);

        $expensesByDate = Expense::whereIn('status', ['approved', 'paid'])
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('expense_date', [$startDate, $endDate]);
            })
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $providerExpenses = Expense::whereIn('status', ['approved', 'paid'])
            ->whereNotNull('payee_type')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('expense_date', [$startDate, $endDate]);
            })
            ->sum('amount');

        return [
            'total_expenses' => $totalExpenses,
            'by_category' => $expensesByCategory,
            'by_date' => $expensesByDate,
            'provider_expenses' => (float) $providerExpenses,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }

    public function getProviderPayoutReport(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ProviderPayment::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $totalOwed = (clone $query)->whereIn('status', ['pending', 'approved'])->sum('amount');
        $totalPaid = (clone $query)->where('status', 'paid')->sum('amount');
        $totalReversed = (clone $query)->where('status', 'reversed')->sum('amount');
        $outstandingBalance = $totalOwed;

        $payoutsByProvider = ProviderPayment::selectRaw('provider_type, provider_id, SUM(amount) as total_amount, 
            SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid_amount,
            SUM(CASE WHEN status IN ("pending", "approved") THEN amount ELSE 0 END) as pending_amount')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('provider_type', 'provider_id')
            ->get()
            ->map(function ($item) {
                $provider = null;
                if (class_exists($item->provider_type)) {
                    $provider = $item->provider_type::find($item->provider_id);
                }
                return [
                    'provider_type' => class_basename($item->provider_type),
                    'provider_id' => $item->provider_id,
                    'provider_name' => $provider?->name ?? 'Unknown',
                    'total_amount' => (float) $item->total_amount,
                    'paid_amount' => (float) $item->paid_amount,
                    'pending_amount' => (float) $item->pending_amount,
                ];
            })
            ->toArray();

        return [
            'total_owed' => (float) $totalOwed,
            'total_paid' => (float) $totalPaid,
            'total_reversed' => (float) $totalReversed,
            'outstanding_balance' => (float) $outstandingBalance,
            'by_provider' => $payoutsByProvider,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }

    public function getProfitLossReport(?string $startDate = null, ?string $endDate = null): array
    {
        $revenueReport = $this->getRevenueReport($startDate, $endDate);
        $expenseReport = $this->getExpenseReport($startDate, $endDate);

        $grossRevenue = $revenueReport['gross_revenue'];
        $refundedRevenue = $revenueReport['refunded_revenue'];
        $netRevenue = $revenueReport['net_revenue'];
        $totalExpenses = $expenseReport['total_expenses'];
        $netProfit = $netRevenue - $totalExpenses;

        return [
            'gross_revenue' => $grossRevenue,
            'refunded_revenue' => $refundedRevenue,
            'net_revenue' => $netRevenue,
            'total_expenses' => $totalExpenses,
            'expenses_by_category' => $expenseReport['by_category'],
            'net_profit' => $netProfit,
            'profit_margin' => $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }

    public function getDashboardSummary(): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $todayRevenue = $this->revenueLedgerService->getNetRevenue($today, $today);
        $monthRevenue = $this->revenueLedgerService->getNetRevenue($startOfMonth, $endOfMonth);

        $todayExpenses = $this->expenseService->getTotalExpenses($today, $today);
        $monthExpenses = $this->expenseService->getTotalExpenses($startOfMonth, $endOfMonth);

        $pendingRefunds = Refund::whereIn('status', ['pending', 'approved', 'processing'])->count();
        $pendingExpenseApprovals = Expense::pendingApproval()->count();
        $pendingProviderPayments = ProviderPayment::pending()->count();

        return [
            'today' => [
                'revenue' => $todayRevenue,
                'expenses' => $todayExpenses,
                'profit' => $todayRevenue - $todayExpenses,
            ],
            'month' => [
                'revenue' => $monthRevenue,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenue - $monthExpenses,
            ],
            'pending' => [
                'refunds' => $pendingRefunds,
                'expense_approvals' => $pendingExpenseApprovals,
                'provider_payments' => $pendingProviderPayments,
            ],
        ];
    }

    public function getOrdersWithoutRevenue(): array
    {
        $orders = Order::completed()
            ->whereNull('revenue_recorded_at')
            ->get(['id', 'order_number', 'total', 'completed_at']);

        $labTestBookings = LabTestBooking::completed()
            ->whereNull('revenue_recorded_at')
            ->get(['id', 'booking_number', 'total', 'completed_at']);

        return [
            'orders' => $orders->toArray(),
            'lab_test_bookings' => $labTestBookings->toArray(),
        ];
    }
}
