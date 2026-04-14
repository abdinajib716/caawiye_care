<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LabTest;
use App\Models\LabTestBooking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LabTestBookingService
{
    /**
     * Get paginated lab test bookings with optional filters.
     */
    public function getPaginatedBookings(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LabTestBooking::with(['customer', 'assignedNurse', 'items']);

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
     * Create a new lab test booking.
     */
    public function createBooking(array $data): LabTestBooking
    {
        return DB::transaction(function () use ($data) {
            // Generate booking number
            $bookingNumber = $this->generateBookingNumber();

            // Extract items data
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Calculate totals
            $subtotal = 0;
            $totalCommission = 0;
            $total = 0;

            foreach ($items as $item) {
                $subtotal += $item['cost'];
                $totalCommission += $item['commission_amount'];
                $total += $item['total'];
            }

            // Create booking with payment and status info
            $booking = LabTestBooking::create([
                ...$data,
                'booking_number' => $bookingNumber,
                'subtotal' => $subtotal,
                'total_commission' => $totalCommission,
                'total' => $total,
                'payment_status' => $data['payment_status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]);

            // Create booking items
            foreach ($items as $item) {
                $labTest = LabTest::find($item['lab_test_id']);
                
                $booking->items()->create([
                    'lab_test_id' => $item['lab_test_id'],
                    'provider_id' => $labTest->provider_id,
                    'test_name' => $labTest->name,
                    'provider_name' => $labTest->provider->name,
                    'cost' => $item['cost'],
                    'commission_percentage' => $item['commission_percentage'],
                    'commission_type' => $item['commission_type'],
                    'commission_amount' => $item['commission_amount'],
                    'profit' => $item['profit'],
                    'total' => $item['total'],
                ]);
            }

            return $booking->load('items');
        });
    }

    /**
     * Generate unique booking number.
     */
    private function generateBookingNumber(): string
    {
        $prefix = 'LTB';
        $date = now()->format('Ymd');
        
        // Get the last booking for today
        $lastBooking = LabTestBooking::whereDate('created_at', today())
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
    public function updateBooking(LabTestBooking $booking, array $data): LabTestBooking
    {
        DB::transaction(function () use ($booking, $data) {
            $booking->update($data);
        });

        return $booking->fresh();
    }

    /**
     * Confirm a booking.
     */
    public function confirmBooking(LabTestBooking $booking): LabTestBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsConfirmed();
        });

        return $booking->fresh();
    }

    /**
     * Mark booking as in progress.
     */
    public function markAsInProgress(LabTestBooking $booking): LabTestBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsInProgress();
        });

        return $booking->fresh();
    }

    /**
     * Complete a booking.
     */
    public function completeBooking(LabTestBooking $booking): LabTestBooking
    {
        DB::transaction(function () use ($booking) {
            $booking->markAsCompleted();
        });

        return $booking->fresh();
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(LabTestBooking $booking, ?string $reason = null): LabTestBooking
    {
        DB::transaction(function () use ($booking, $reason) {
            $booking->markAsCancelled($reason);
        });

        return $booking->fresh();
    }

    /**
     * Delete a booking.
     */
    public function deleteBooking(LabTestBooking $booking): bool
    {
        return $booking->delete();
    }

    /**
     * Get booking statistics.
     */
    public function getBookingStatistics(): array
    {
        return [
            'total_bookings' => LabTestBooking::count(),
            'pending_bookings' => LabTestBooking::pending()->count(),
            'confirmed_bookings' => LabTestBooking::confirmed()->count(),
            'in_progress_bookings' => LabTestBooking::inProgress()->count(),
            'completed_bookings' => LabTestBooking::completed()->count(),
            'cancelled_bookings' => LabTestBooking::cancelled()->count(),
        ];
    }

    /**
     * Get all active lab tests.
     */
    public function getActiveLabTests(): Collection
    {
        return LabTest::with('provider')->active()->orderBy('name', 'asc')->get();
    }
}
