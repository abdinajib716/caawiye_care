<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\LabTestBooking;
use App\Models\MedicineOrder;
use App\Models\Order;
use App\Models\Refund;
use App\Models\ReportCollection;
use App\Models\ScanImagingBooking;

class DashboardService
{
    public function getMetrics(): array
    {
        $ordersRevenue = (float) Order::query()
            ->where('payment_status', 'completed')
            ->sum('total');

        $medicineRevenue = (float) MedicineOrder::query()
            ->where('payment_status', 'completed')
            ->sum('total');

        $labRevenue = (float) LabTestBooking::query()
            ->where('payment_status', 'paid')
            ->sum('total');

        $scanRevenue = (float) ScanImagingBooking::query()
            ->where('payment_status', 'paid')
            ->sum('total');

        $collectionRevenue = (float) ReportCollection::query()
            ->where('payment_status', ReportCollection::PAYMENT_VERIFIED)
            ->sum('total_amount');

        $completedRefundAmount = (float) Refund::query()
            ->where('status', 'completed')
            ->sum('refund_amount');

        $todayOrdersRevenue = (float) Order::query()
            ->where('payment_status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayMedicineRevenue = (float) MedicineOrder::query()
            ->where('payment_status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayLabRevenue = (float) LabTestBooking::query()
            ->where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayScanRevenue = (float) ScanImagingBooking::query()
            ->where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayCollectionRevenue = (float) ReportCollection::query()
            ->where('payment_status', ReportCollection::PAYMENT_VERIFIED)
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $todayRefundAmount = (float) Refund::query()
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('refund_amount');

        $labActive = (int) LabTestBooking::query()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        $scanActive = (int) ScanImagingBooking::query()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        $grossVerifiedRevenue = $ordersRevenue + $medicineRevenue + $labRevenue + $scanRevenue + $collectionRevenue;
        $todayGrossRevenue = $todayOrdersRevenue + $todayMedicineRevenue + $todayLabRevenue + $todayScanRevenue + $todayCollectionRevenue;

        return [
            'customers_total' => (int) Customer::query()->count(),
            'customers_new_today' => (int) Customer::query()->whereDate('created_at', today())->count(),

            'orders_paid' => (int) Order::query()->where('payment_status', 'completed')->count(),
            'orders_processing' => (int) Order::query()->whereIn('status', ['pending', 'processing'])->count(),
            'orders_refunded' => (int) Order::query()->where('payment_status', 'refunded')->count(),
            'orders_new_today' => (int) Order::query()->whereDate('created_at', today())->count(),

            'appointments_active' => (int) Appointment::query()->whereIn('status', ['scheduled', 'confirmed'])->count(),
            'appointments_completed_today' => (int) Appointment::query()->where('status', 'completed')->whereDate('completed_at', today())->count(),
            'appointments_new_today' => (int) Appointment::query()->whereDate('created_at', today())->count(),

            'lab_active' => $labActive,
            'lab_completed_today' => (int) LabTestBooking::query()->where('status', 'completed')->whereDate('completed_at', today())->count(),
            'lab_new_today' => (int) LabTestBooking::query()->whereDate('created_at', today())->count(),

            'scan_active' => $scanActive,
            'scan_completed_today' => (int) ScanImagingBooking::query()->where('status', 'completed')->whereDate('completed_at', today())->count(),
            'scan_new_today' => (int) ScanImagingBooking::query()->whereDate('created_at', today())->count(),

            'diagnostics_active' => $labActive + $scanActive,

            'medicine_pending_delivery' => (int) MedicineOrder::query()->whereIn('status', ['pending', 'in_office'])->count(),
            'medicine_delivered_today' => (int) MedicineOrder::query()->where('status', 'delivered')->whereDate('updated_at', today())->count(),
            'medicine_new_today' => (int) MedicineOrder::query()->whereDate('created_at', today())->count(),

            'collections_open' => (int) ReportCollection::query()->whereIn('status', [
                ReportCollection::STATUS_PENDING,
                ReportCollection::STATUS_IN_PROGRESS,
            ])->count(),
            'collections_completed_today' => (int) ReportCollection::query()->where('status', ReportCollection::STATUS_COMPLETED)->whereDate('completed_at', today())->count(),
            'collections_new_today' => (int) ReportCollection::query()->whereDate('created_at', today())->count(),

            'refunds_open' => (int) Refund::query()->whereIn('status', ['pending', 'approved', 'processing'])->count(),
            'refunds_completed_today' => (int) Refund::query()->where('status', 'completed')->whereDate('refund_executed_at', today())->count(),

            'gross_verified_revenue' => $grossVerifiedRevenue,
            'completed_refunds_amount' => $completedRefundAmount,
            'net_verified_revenue' => $grossVerifiedRevenue - $completedRefundAmount,
            'today_net_revenue' => $todayGrossRevenue - $todayRefundAmount,
            'today_new_intake' => (int) (
                Order::query()->whereDate('created_at', today())->count()
                + Appointment::query()->whereDate('created_at', today())->count()
                + LabTestBooking::query()->whereDate('created_at', today())->count()
                + ScanImagingBooking::query()->whereDate('created_at', today())->count()
                + MedicineOrder::query()->whereDate('created_at', today())->count()
                + ReportCollection::query()->whereDate('created_at', today())->count()
            ),
        ];
    }
}
