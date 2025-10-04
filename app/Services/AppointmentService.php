<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Get paginated appointments with optional filters.
     */
    public function getPaginatedAppointments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Appointment::with(['customer', 'hospital', 'order']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply hospital filter
        if (!empty($filters['hospital_id'])) {
            $query->where('hospital_id', $filters['hospital_id']);
        }

        // Apply date range filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        // Apply upcoming/past filter
        if (!empty($filters['time_filter'])) {
            if ($filters['time_filter'] === 'upcoming') {
                $query->upcoming();
            } elseif ($filters['time_filter'] === 'past') {
                $query->past();
            }
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'appointment_time';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new appointment.
     */
    public function createAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            return Appointment::create($data);
        });
    }

    /**
     * Update an existing appointment.
     */
    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        DB::transaction(function () use ($appointment, $data) {
            $appointment->update($data);
        });

        return $appointment->fresh();
    }

    /**
     * Reschedule an appointment.
     */
    public function rescheduleAppointment(Appointment $appointment, string $newDateTime, ?string $notes = null): Appointment
    {
        DB::transaction(function () use ($appointment, $newDateTime, $notes) {
            $appointment->update([
                'appointment_time' => $newDateTime,
                'status' => 'scheduled',
                'notes' => $notes ?? $appointment->notes,
                'confirmed_at' => null,
            ]);
        });

        return $appointment->fresh();
    }

    /**
     * Cancel an appointment.
     */
    public function cancelAppointment(Appointment $appointment, ?string $reason = null): Appointment
    {
        DB::transaction(function () use ($appointment, $reason) {
            $appointment->markAsCancelled($reason);
        });

        return $appointment->fresh();
    }

    /**
     * Confirm an appointment.
     */
    public function confirmAppointment(Appointment $appointment): Appointment
    {
        DB::transaction(function () use ($appointment) {
            $appointment->markAsConfirmed();
        });

        return $appointment->fresh();
    }

    /**
     * Complete an appointment.
     */
    public function completeAppointment(Appointment $appointment): Appointment
    {
        DB::transaction(function () use ($appointment) {
            $appointment->markAsCompleted();
        });

        return $appointment->fresh();
    }

    /**
     * Mark appointment as no-show.
     */
    public function markAsNoShow(Appointment $appointment): Appointment
    {
        DB::transaction(function () use ($appointment) {
            $appointment->markAsNoShow();
        });

        return $appointment->fresh();
    }

    /**
     * Delete an appointment (soft delete).
     */
    public function deleteAppointment(Appointment $appointment): bool
    {
        return $appointment->delete();
    }

    /**
     * Get upcoming appointments.
     */
    public function getUpcomingAppointments(int $limit = 10): Collection
    {
        return Appointment::with(['customer', 'hospital'])
            ->upcoming()
            ->orderBy('appointment_time', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get appointments for a specific customer.
     */
    public function getCustomerAppointments(int $customerId, array $filters = []): Collection
    {
        $query = Appointment::with(['hospital', 'order'])
            ->where('customer_id', $customerId);

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('appointment_time', 'desc')->get();
    }

    /**
     * Get appointments for a specific hospital.
     */
    public function getHospitalAppointments(int $hospitalId, array $filters = []): Collection
    {
        $query = Appointment::with(['customer', 'order'])
            ->where('hospital_id', $hospitalId);

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply date filter
        if (!empty($filters['date'])) {
            $query->whereDate('appointment_time', $filters['date']);
        }

        return $query->orderBy('appointment_time', 'asc')->get();
    }

    /**
     * Get appointment statistics.
     */
    public function getAppointmentStatistics(): array
    {
        $totalAppointments = Appointment::count();
        $scheduledAppointments = Appointment::scheduled()->count();
        $confirmedAppointments = Appointment::confirmed()->count();
        $completedAppointments = Appointment::completed()->count();
        $cancelledAppointments = Appointment::cancelled()->count();
        $noShowAppointments = Appointment::noShow()->count();
        $upcomingAppointments = Appointment::upcoming()->count();

        // Get appointments by hospital
        $appointmentsByHospital = Appointment::with('hospital')
            ->selectRaw('hospital_id, COUNT(*) as count')
            ->groupBy('hospital_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'hospital_name' => $item->hospital->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            })
            ->toArray();

        return [
            'total_appointments' => $totalAppointments,
            'scheduled_appointments' => $scheduledAppointments,
            'confirmed_appointments' => $confirmedAppointments,
            'completed_appointments' => $completedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'no_show_appointments' => $noShowAppointments,
            'upcoming_appointments' => $upcomingAppointments,
            'appointments_by_hospital' => $appointmentsByHospital,
        ];
    }

    /**
     * Get appointments for a specific date.
     */
    public function getAppointmentsByDate(string $date, ?int $hospitalId = null): Collection
    {
        $query = Appointment::with(['customer', 'hospital'])
            ->whereDate('appointment_time', $date);

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        return $query->orderBy('appointment_time', 'asc')->get();
    }

    /**
     * Check if appointment time is available.
     */
    public function isTimeAvailable(int $hospitalId, string $dateTime, ?int $excludeAppointmentId = null): bool
    {
        $query = Appointment::where('hospital_id', $hospitalId)
            ->where('appointment_time', $dateTime)
            ->whereIn('status', ['scheduled', 'confirmed']);

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return !$query->exists();
    }
}

