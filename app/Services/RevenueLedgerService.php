<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RevenueLedger;
use App\Models\Order;
use App\Models\LabTestBooking;
use App\Models\MedicineOrder;
use App\Models\ScanImagingBooking;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class RevenueLedgerService
{
    public function recordRevenueForOrder(Order $order): RevenueLedger
    {
        if ($order->hasRevenueRecorded()) {
            throw new \Exception('Revenue already recorded for this order.');
        }

        return DB::transaction(function () use ($order) {
            $entry = RevenueLedger::recordRevenue(
                Order::class,
                $order->id,
                (float) $order->total,
                'order',
                "Revenue from Order #{$order->order_number}"
            );

            $order->update(['revenue_recorded_at' => now()]);

            return $entry;
        });
    }

    public function recordRevenueForLabTestBooking(LabTestBooking $booking): RevenueLedger
    {
        if ($booking->revenue_recorded_at) {
            throw new \Exception('Revenue already recorded for this booking.');
        }

        return DB::transaction(function () use ($booking) {
            $entry = RevenueLedger::recordRevenue(
                LabTestBooking::class,
                $booking->id,
                (float) $booking->total,
                'lab_test',
                "Revenue from Lab Test Booking #{$booking->booking_number}"
            );

            $booking->update(['revenue_recorded_at' => now()]);

            return $entry;
        });
    }

    public function recordRevenueForMedicineOrder(MedicineOrder $order): RevenueLedger
    {
        if ($order->revenue_recorded_at) {
            throw new \Exception('Revenue already recorded for this order.');
        }

        return DB::transaction(function () use ($order) {
            $entry = RevenueLedger::recordRevenue(
                MedicineOrder::class,
                $order->id,
                (float) $order->total,
                'medicine',
                "Revenue from Medicine Order #{$order->order_number}"
            );

            $order->update(['revenue_recorded_at' => now()]);

            return $entry;
        });
    }

    public function recordRevenueForScanImagingBooking(ScanImagingBooking $booking): RevenueLedger
    {
        if ($booking->revenue_recorded_at) {
            throw new \Exception('Revenue already recorded for this booking.');
        }

        return DB::transaction(function () use ($booking) {
            $entry = RevenueLedger::recordRevenue(
                ScanImagingBooking::class,
                $booking->id,
                (float) $booking->total,
                'scan_imaging',
                "Revenue from Scan/Imaging Booking #{$booking->booking_number}"
            );

            $booking->update(['revenue_recorded_at' => now()]);

            return $entry;
        });
    }

    public function reverseRevenue(string $orderType, int $orderId, float $amount, int $refundId): RevenueLedger
    {
        $serviceType = $this->getServiceTypeFromOrderType($orderType);

        return RevenueLedger::recordReversal(
            $orderType,
            $orderId,
            $amount,
            $serviceType,
            $refundId,
            "Revenue reversal for refund"
        );
    }

    public function getTotalRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $query = RevenueLedger::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return (float) $query->sum('amount');
    }

    public function getNetRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        return $this->getTotalRevenue($startDate, $endDate);
    }

    public function getRevenueByServiceType(?string $startDate = null, ?string $endDate = null): array
    {
        $query = RevenueLedger::query()
            ->selectRaw('service_type, SUM(amount) as total')
            ->groupBy('service_type');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->pluck('total', 'service_type')->toArray();
    }

    public function getRevenueByDate(?string $startDate = null, ?string $endDate = null): array
    {
        $query = RevenueLedger::query()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->pluck('total', 'date')->toArray();
    }

    public function getGrossRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $query = RevenueLedger::revenue();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return (float) $query->sum('amount');
    }

    public function getRefundedRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $query = RevenueLedger::reversal();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return abs((float) $query->sum('amount'));
    }

    private function getServiceTypeFromOrderType(string $orderType): string
    {
        return match ($orderType) {
            Order::class => 'order',
            LabTestBooking::class => 'lab_test',
            MedicineOrder::class => 'medicine',
            ScanImagingBooking::class => 'scan_imaging',
            Appointment::class => 'appointment',
            default => 'other',
        };
    }
}
