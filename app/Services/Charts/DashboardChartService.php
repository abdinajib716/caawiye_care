<?php

declare(strict_types=1);

namespace App\Services\Charts;

use App\Models\Appointment;
use App\Models\LabTestBooking;
use App\Models\MedicineOrder;
use App\Models\Order;
use App\Models\Refund;
use App\Models\ReportCollection;
use App\Models\ScanImagingBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardChartService extends ChartService
{
    public function getRevenueTrendData(string $period = 'last_6_months'): array
    {
        [$startDate, $endDate] = $this->getDateRange($period);

        $isLessThanMonth = $startDate->diffInMonths($endDate) === 0;
        $labelFormat = $isLessThanMonth ? 'd M Y' : 'M Y';
        $dbFormat = $isLessThanMonth ? 'Y-m-d' : 'Y-m';
        $intervalMethod = $isLessThanMonth ? 'addDay' : 'addMonth';

        $labels = $this->generateLabels($startDate, $endDate, $labelFormat, $intervalMethod);

        $grossRevenue = $this->aggregateRevenue(Order::class, 'payment_status', 'completed', 'total', 'created_at', $startDate, $endDate, $isLessThanMonth)
            ->mergeRecursive($this->aggregateRevenue(MedicineOrder::class, 'payment_status', 'completed', 'total', 'created_at', $startDate, $endDate, $isLessThanMonth))
            ->mergeRecursive($this->aggregateRevenue(LabTestBooking::class, 'payment_status', 'paid', 'total', 'created_at', $startDate, $endDate, $isLessThanMonth))
            ->mergeRecursive($this->aggregateRevenue(ScanImagingBooking::class, 'payment_status', 'paid', 'total', 'created_at', $startDate, $endDate, $isLessThanMonth))
            ->mergeRecursive($this->aggregateRevenue(ReportCollection::class, 'payment_status', ReportCollection::PAYMENT_VERIFIED, 'total_amount', 'created_at', $startDate, $endDate, $isLessThanMonth))
            ->map(function ($value) {
                if (is_array($value)) {
                    return array_sum(array_map('floatval', $value));
                }

                return (float) $value;
            });

        $refunds = $this->aggregateRevenue(Refund::class, 'status', 'completed', 'refund_amount', 'refund_executed_at', $startDate, $endDate, $isLessThanMonth);

        $data = $labels->map(function (string $label) use ($grossRevenue, $refunds, $labelFormat, $dbFormat) {
            $key = Carbon::createFromFormat($labelFormat, $label)->format($dbFormat);

            return round(((float) ($grossRevenue[$key] ?? 0)) - ((float) ($refunds[$key] ?? 0)), 2);
        });

        return [
            'labels' => $labels->toArray(),
            'data' => $data->toArray(),
        ];
    }

    public function getWorkloadBreakdownData(): array
    {
        return [
            'labels' => [
                __('Appointments'),
                __('Lab Tests'),
                __('Scan & Imaging'),
                __('Medicine'),
                __('Collections'),
                __('Refunds'),
            ],
            'data' => [
                Appointment::query()->whereIn('status', ['scheduled', 'confirmed'])->count(),
                LabTestBooking::query()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
                ScanImagingBooking::query()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
                MedicineOrder::query()->whereIn('status', ['pending', 'in_office'])->count(),
                ReportCollection::query()->whereIn('status', [ReportCollection::STATUS_PENDING, ReportCollection::STATUS_IN_PROGRESS])->count(),
                Refund::query()->whereIn('status', ['pending', 'approved', 'processing'])->count(),
            ],
        ];
    }

    protected function aggregateRevenue(
        string $modelClass,
        string $statusColumn,
        string $statusValue,
        string $amountColumn,
        string $dateColumn,
        Carbon $startDate,
        Carbon $endDate,
        bool $isLessThanMonth
    ): Collection {
        /** @var Model $modelClass */
        $query = $modelClass::query()
            ->where($statusColumn, $statusValue)
            ->whereNotNull($dateColumn)
            ->whereBetween($dateColumn, [$startDate, $endDate]);

        $driver = DB::connection()->getDriverName();

        if ($isLessThanMonth) {
            $selectRaw = "DATE($dateColumn) as period_key, SUM($amountColumn) as total";
        } else {
            $selectRaw = $driver === 'sqlite'
                ? "strftime('%Y-%m', $dateColumn) as period_key, SUM($amountColumn) as total"
                : "DATE_FORMAT($dateColumn, '%Y-%m') as period_key, SUM($amountColumn) as total";
        }

        return $query
            ->selectRaw($selectRaw)
            ->groupBy('period_key')
            ->orderBy('period_key')
            ->pluck('total', 'period_key')
            ->map(fn ($total) => (float) $total);
    }
}
