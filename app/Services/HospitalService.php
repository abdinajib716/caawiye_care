<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Hospital;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HospitalService
{
    /**
     * Get paginated hospitals with optional filters.
     */
    public function getPaginatedHospitals(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Hospital::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new hospital.
     */
    public function createHospital(array $data): Hospital
    {
        return Hospital::create($data);
    }

    /**
     * Update an existing hospital.
     */
    public function updateHospital(Hospital $hospital, array $data): Hospital
    {
        $hospital->update($data);
        return $hospital->fresh();
    }

    /**
     * Delete a hospital (soft delete).
     */
    public function deleteHospital(Hospital $hospital): bool
    {
        return $hospital->delete();
    }

    /**
     * Restore a soft-deleted hospital.
     */
    public function restoreHospital(Hospital $hospital): bool
    {
        return $hospital->restore();
    }

    /**
     * Force delete a hospital (permanent deletion).
     */
    public function forceDeleteHospital(Hospital $hospital): bool
    {
        return $hospital->forceDelete();
    }

    /**
     * Get active hospitals for selection.
     */
    public function getActiveHospitals(): Collection
    {
        return Hospital::active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Search hospitals by query.
     */
    public function searchHospitals(string $query, array $filters = []): Collection
    {
        $queryBuilder = Hospital::search($query);

        // Apply additional filters
        if (!empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        return $queryBuilder->limit(50)->get();
    }

    /**
     * Get hospital statistics.
     */
    public function getHospitalStatistics(): array
    {
        $totalHospitals = Hospital::count();
        $activeHospitals = Hospital::active()->count();
        $inactiveHospitals = Hospital::where('status', 'inactive')->count();
        
        // Get hospitals with appointment counts
        $hospitalsWithAppointments = Hospital::withCount('appointments')
            ->orderByDesc('appointments_count')
            ->limit(5)
            ->get()
            ->map(function ($hospital) {
                return [
                    'name' => $hospital->name,
                    'appointments_count' => $hospital->appointments_count,
                ];
            })
            ->toArray();

        return [
            'total_hospitals' => $totalHospitals,
            'active_hospitals' => $activeHospitals,
            'inactive_hospitals' => $inactiveHospitals,
            'top_hospitals' => $hospitalsWithAppointments,
        ];
    }

    /**
     * Bulk update hospital status.
     */
    public function bulkUpdateStatus(array $hospitalIds, string $status): int
    {
        return Hospital::whereIn('id', $hospitalIds)
            ->update(['status' => $status]);
    }

    /**
     * Bulk delete hospitals.
     */
    public function bulkDeleteHospitals(array $hospitalIds): int
    {
        $hospitals = Hospital::whereIn('id', $hospitalIds)->get();
        $deletedCount = 0;

        foreach ($hospitals as $hospital) {
            if ($hospital->delete()) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get recently created hospitals.
     */
    public function getRecentHospitals(int $limit = 10): Collection
    {
        return Hospital::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if hospital name exists for another hospital.
     */
    public function nameExistsForOtherHospital(string $name, ?int $excludeHospitalId = null): bool
    {
        $query = Hospital::where('name', $name);

        if ($excludeHospitalId) {
            $query->where('id', '!=', $excludeHospitalId);
        }

        return $query->exists();
    }
}

