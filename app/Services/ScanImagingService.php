<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ScanImagingBooking;
use App\Models\ScanImagingService as ScanImagingServiceModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ScanImagingService
{
    /**
     * Get paginated scan imaging bookings with optional filters.
     */
    public function getPaginatedBookings(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ScanImagingBooking::with(['customer', 'scanImagingService', 'provider']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply payment status filter
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new scan imaging booking.
     */
    public function createBooking(array $data): ScanImagingBooking
    {
        return DB::transaction(function () use ($data) {
            // Generate booking number
            $bookingNumber = $this->generateBookingNumber();

            // Get service details
            $service = ScanImagingServiceModel::with('provider')->find($data['scan_imaging_service_id']);

            // Calculate total based on commission type
            $total = $service->commission_type === 'bill_customer' 
                ? (float) $service->cost + (float) $service->commission_amount 
                : (float) $service->cost;

            // Create booking with payment and status info
            return ScanImagingBooking::create([
                ...$data,
                'booking_number' => $bookingNumber,
                'provider_id' => $service->provider_id,
                'service_name' => $service->service_name,
                'provider_name' => $service->provider->name,
                'cost' => $service->cost,
                'commission_percentage' => $service->commission_percentage,
                'commission_type' => $service->commission_type,
                'commission_amount' => $service->commission_amount,
                'total' => $total,
                'payment_status' => $data['payment_status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]);
        });
    }

    /**
     * Generate unique booking number.
     */
    private function generateBookingNumber(): string
    {
        $prefix = 'SIB';
        $date = now()->format('Ymd');
        
        // Get the last booking for today
        $lastBooking = ScanImagingBooking::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_number, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$date}{$newNumber}";
    }

    /**
     * Update a booking.
     */
    public function updateBooking(ScanImagingBooking $booking, array $data): ScanImagingBooking
    {
        DB::transaction(function () use ($booking, $data) {
            $booking->update($data);
        });

        return $booking->fresh();
    }

    /**
     * Confirm a booking.
     */
    public function confirmBooking(ScanImagingBooking $booking): ScanImagingBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsConfirmed();
        });

        return $booking->fresh();
    }

    /**
     * Mark booking as in progress.
     */
    public function markAsInProgress(ScanImagingBooking $booking): ScanImagingBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsInProgress();
        });

        return $booking->fresh();
    }

    /**
     * Complete a booking.
     */
    public function completeBooking(ScanImagingBooking $booking): ScanImagingBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsCompleted();
        });

        return $booking->fresh();
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(ScanImagingBooking $booking, ?string $reason = null): ScanImagingBooking
    {
        DB::transaction(function () use ($booking, $reason) {
            $booking->markAsCancelled($reason);
        });

        return $booking->fresh();
    }

    /**
     * Delete a booking.
     */
    public function deleteBooking(ScanImagingBooking $booking): bool
    {
        return $booking->delete();
    }

    /**
     * Get booking statistics.
     */
    public function getBookingStatistics(): array
    {
        return [
            'total_bookings' => ScanImagingBooking::count(),
            'pending_bookings' => ScanImagingBooking::pending()->count(),
            'confirmed_bookings' => ScanImagingBooking::confirmed()->count(),
            'in_progress_bookings' => ScanImagingBooking::inProgress()->count(),
            'completed_bookings' => ScanImagingBooking::completed()->count(),
            'cancelled_bookings' => ScanImagingBooking::cancelled()->count(),
        ];
    }

    /**
     * Get all active scan imaging services.
     */
    public function getActiveServices(): Collection
    {
        return ScanImagingServiceModel::with('provider')->active()->orderBy('service_name', 'asc')->get();
    }
}
