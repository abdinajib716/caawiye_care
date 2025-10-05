<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DoctorService
{
    /**
     * Get paginated doctors with optional filters.
     */
    public function getPaginatedDoctors(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Doctor::query()->with('hospital');

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

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new doctor.
     */
    public function createDoctor(array $data): Doctor
    {
        return Doctor::create($data);
    }

    /**
     * Update an existing doctor.
     */
    public function updateDoctor(Doctor $doctor, array $data): Doctor
    {
        $doctor->update($data);
        return $doctor->fresh();
    }

    /**
     * Delete a doctor (soft delete).
     */
    public function deleteDoctor(Doctor $doctor): bool
    {
        return $doctor->delete();
    }

    /**
     * Restore a soft-deleted doctor.
     */
    public function restoreDoctor(Doctor $doctor): bool
    {
        return $doctor->restore();
    }

    /**
     * Force delete a doctor (permanent deletion).
     */
    public function forceDeleteDoctor(Doctor $doctor): bool
    {
        return $doctor->forceDelete();
    }

    /**
     * Get active doctors for selection.
     */
    public function getActiveDoctors(): Collection
    {
        return Doctor::active()
            ->with('hospital')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active doctors by hospital.
     */
    public function getActiveDoctorsByHospital(int $hospitalId): Collection
    {
        return Doctor::active()
            ->byHospital($hospitalId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Search doctors by query.
     */
    public function searchDoctors(string $query, array $filters = []): Collection
    {
        $queryBuilder = Doctor::search($query);

        // Apply additional filters
        if (!empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        if (!empty($filters['hospital_id'])) {
            $queryBuilder->where('hospital_id', $filters['hospital_id']);
        }

        return $queryBuilder->limit(50)->get();
    }

    /**
     * Get doctor statistics.
     */
    public function getDoctorStatistics(): array
    {
        $totalDoctors = Doctor::count();
        $activeDoctors = Doctor::active()->count();
        $inactiveDoctors = Doctor::where('status', 'inactive')->count();
        
        // Get doctors by hospital
        $doctorsByHospital = Doctor::with('hospital')
            ->selectRaw('hospital_id, count(*) as doctor_count')
            ->groupBy('hospital_id')
            ->orderByDesc('doctor_count')
            ->limit(5)
            ->get()
            ->map(function ($doctor) {
                return [
                    'hospital_name' => $doctor->hospital->name ?? 'Unknown',
                    'doctor_count' => $doctor->doctor_count,
                ];
            })
            ->toArray();

        return [
            'total_doctors' => $totalDoctors,
            'active_doctors' => $activeDoctors,
            'inactive_doctors' => $inactiveDoctors,
            'top_hospitals' => $doctorsByHospital,
        ];
    }

    /**
     * Bulk update doctor status.
     */
    public function bulkUpdateStatus(array $doctorIds, string $status): int
    {
        return Doctor::whereIn('id', $doctorIds)
            ->update(['status' => $status]);
    }

    /**
     * Bulk delete doctors.
     */
    public function bulkDeleteDoctors(array $doctorIds): int
    {
        $doctors = Doctor::whereIn('id', $doctorIds)->get();
        $deletedCount = 0;

        foreach ($doctors as $doctor) {
            if ($doctor->delete()) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get recently created doctors.
     */
    public function getRecentDoctors(int $limit = 10): Collection
    {
        return Doctor::with('hospital')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

