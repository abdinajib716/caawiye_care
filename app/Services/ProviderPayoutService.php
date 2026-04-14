<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Provider;
use App\Models\ProviderPayment;
use App\Models\LabTestBooking;
use App\Models\LabTestBookingItem;
use App\Models\ScanImagingBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProviderPayoutService
{
    /**
     * Search providers by name or ID.
     */
    public function searchProviders(string $search): Collection
    {
        return Provider::where('name', 'like', "%{$search}%")
            ->orWhere('id', $search)
            ->limit(10)
            ->get(['id', 'name', 'phone', 'status']);
    }

    /**
     * Get provider summary with financial information.
     */
    public function getProviderSummary(int $providerId, ?string $startDate = null, ?string $endDate = null): array
    {
        $provider = Provider::findOrFail($providerId);

        // Get all earnings for this provider
        $earnings = $this->getProviderEarnings($providerId, $startDate, $endDate);

        $totalEarned = $earnings->sum('provider_amount');
        $totalPaid = $earnings->where('provider_payment_status', 'paid')->sum('provider_amount');
        $totalReversed = $earnings->where('provider_payment_status', 'reversed')->sum('provider_amount');
        $outstandingBalance = $earnings->where('provider_payment_status', 'unpaid')->sum('provider_amount');

        return [
            'provider' => $provider,
            'total_earned' => (float) $totalEarned,
            'total_paid' => (float) $totalPaid,
            'total_reversed' => (float) $totalReversed,
            'outstanding_balance' => (float) $outstandingBalance,
            'unpaid_orders_count' => $earnings->where('provider_payment_status', 'unpaid')->count(),
        ];
    }

    /**
     * Get provider earnings from all order types.
     */
    public function getProviderEarnings(int $providerId, ?string $startDate = null, ?string $endDate = null, ?string $statusFilter = null): Collection
    {
        $earnings = collect();

        // Get lab test booking earnings (via items -> lab_tests -> provider)
        $labTestItems = LabTestBookingItem::with(['labTestBooking', 'labTest'])
            ->whereHas('labTest', function ($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })
            ->whereHas('labTestBooking', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed');
                if ($startDate && $endDate) {
                    $q->whereBetween('completed_at', [$startDate, $endDate]);
                }
            })
            ->get();

        foreach ($labTestItems as $item) {
            $booking = $item->labTestBooking;
            $cost = (float) $item->cost;
            $commissionAmount = (float) $item->commission_amount;
            $commissionType = $item->commission_type ?? 'bill_customer';
            
            // Calculate provider amount based on commission type:
            // - bill_customer: Provider gets full cost (commission was charged to customer separately)
            // - bill_provider: Provider gets cost minus commission (commission deducted from provider payout)
            if ($commissionType === 'bill_provider') {
                $providerAmount = $cost - $commissionAmount;
            } else {
                $providerAmount = $cost;
            }

            $earnings->push([
                'id' => 'lab_' . $item->id,
                'order_id' => $booking->booking_number,
                'order_type' => 'Lab Test',
                'service_name' => $item->labTest->name ?? 'Lab Test',
                'order_date' => $booking->completed_at,
                'provider_amount' => $providerAmount,
                'commission_type' => $commissionType,
                'commission_amount' => $commissionAmount,
                'original_cost' => $cost,
                'provider_payment_status' => $booking->provider_payment_status ?? 'unpaid',
                'booking_id' => $booking->id,
                'booking_type' => LabTestBooking::class,
                'item_id' => $item->id,
            ]);
        }

        // Get scan imaging booking earnings
        $scanBookings = ScanImagingBooking::with(['scanImagingService'])
            ->where('provider_id', $providerId)
            ->where('status', 'completed')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('completed_at', [$startDate, $endDate]);
            })
            ->get();

        foreach ($scanBookings as $booking) {
            $cost = (float) $booking->cost;
            $commissionAmount = (float) $booking->commission_amount;
            $commissionType = $booking->commission_type ?? 'bill_customer';
            
            // Calculate provider amount based on commission type:
            // - bill_customer: Provider gets full cost (commission was charged to customer separately)
            // - bill_provider: Provider gets cost minus commission (commission deducted from provider payout)
            if ($commissionType === 'bill_provider') {
                $providerAmount = $cost - $commissionAmount;
            } else {
                $providerAmount = $cost;
            }

            $earnings->push([
                'id' => 'scan_' . $booking->id,
                'order_id' => $booking->booking_number,
                'order_type' => 'Scan & Imaging',
                'service_name' => $booking->service_name,
                'order_date' => $booking->completed_at,
                'provider_amount' => $providerAmount,
                'commission_type' => $commissionType,
                'commission_amount' => $commissionAmount,
                'original_cost' => $cost,
                'provider_payment_status' => $booking->provider_payment_status ?? 'unpaid',
                'booking_id' => $booking->id,
                'booking_type' => ScanImagingBooking::class,
                'item_id' => null,
            ]);
        }

        // Apply status filter
        if ($statusFilter && $statusFilter !== 'all') {
            $earnings = $earnings->filter(function ($earning) use ($statusFilter) {
                return $earning['provider_payment_status'] === $statusFilter;
            });
        }

        // Sort by order date descending
        return $earnings->sortByDesc('order_date')->values();
    }

    /**
     * Pay provider for all outstanding earnings.
     */
    public function payProvider(
        int $providerId,
        string $paymentMethod,
        ?string $transactionReference = null,
        ?string $notes = null
    ): array {
        $provider = Provider::findOrFail($providerId);

        // Get unpaid earnings
        $unpaidEarnings = $this->getProviderEarnings($providerId, null, null, 'unpaid');

        if ($unpaidEarnings->isEmpty()) {
            throw new \Exception(__('No outstanding balance to pay.'));
        }

        $totalAmount = $unpaidEarnings->sum('provider_amount');
        $orderCount = $unpaidEarnings->count();

        DB::beginTransaction();
        try {
            // Create provider payment record
            $payment = ProviderPayment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'provider_id' => $providerId,
                'provider_type' => Provider::class,
                'amount' => $totalAmount,
                'status' => 'paid',
                'payment_method' => $paymentMethod,
                'payment_reference' => $transactionReference,
                'notes' => $notes,
                'paid_at' => now(),
                'paid_by' => auth()->id(),
            ]);

            // Update all related bookings
            foreach ($unpaidEarnings as $earning) {
                if ($earning['booking_type'] === LabTestBooking::class) {
                    LabTestBooking::where('id', $earning['booking_id'])
                        ->update(['provider_payment_status' => 'paid']);
                } elseif ($earning['booking_type'] === ScanImagingBooking::class) {
                    ScanImagingBooking::where('id', $earning['booking_id'])
                        ->update(['provider_payment_status' => 'paid']);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'total_amount' => $totalAmount,
                'order_count' => $orderCount,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse a provider earning (e.g., due to cancellation or refund).
     */
    public function reverseEarning(string $bookingType, int $bookingId, string $reason): bool
    {
        DB::beginTransaction();
        try {
            if ($bookingType === LabTestBooking::class || $bookingType === 'lab_test') {
                $booking = LabTestBooking::findOrFail($bookingId);
                
                if ($booking->provider_payment_status === 'paid') {
                    throw new \Exception(__('Cannot reverse a paid earning.'));
                }
                
                $booking->update(['provider_payment_status' => 'reversed']);
            } elseif ($bookingType === ScanImagingBooking::class || $bookingType === 'scan_imaging') {
                $booking = ScanImagingBooking::findOrFail($bookingId);
                
                if ($booking->provider_payment_status === 'paid') {
                    throw new \Exception(__('Cannot reverse a paid earning.'));
                }
                
                $booking->update(['provider_payment_status' => 'reversed']);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate a unique payment number.
     */
    private function generatePaymentNumber(): string
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $lastPayment = ProviderPayment::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? ((int) substr($lastPayment->payment_number, -4)) + 1 : 1;

        return $prefix . $date . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get all providers with their outstanding balances.
     */
    public function getProvidersWithBalances(): Collection
    {
        $providers = Provider::active()->get();

        return $providers->map(function ($provider) {
            $summary = $this->getProviderSummary($provider->id);
            return [
                'id' => $provider->id,
                'name' => $provider->name,
                'phone' => $provider->phone,
                'outstanding_balance' => $summary['outstanding_balance'],
                'total_earned' => $summary['total_earned'],
                'total_paid' => $summary['total_paid'],
            ];
        })->filter(function ($provider) {
            return $provider['total_earned'] > 0;
        })->values();
    }
}
