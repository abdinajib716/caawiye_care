<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Provider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProviderService
{
    /**
     * Get paginated providers with optional filters.
     */
    public function getPaginatedProviders(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Provider::query();

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
     * Create a new provider.
     */
    public function createProvider(array $data): Provider
    {
        return DB::transaction(function () use ($data) {
            return Provider::create($data);
        });
    }

    /**
     * Update an existing provider.
     */
    public function updateProvider(Provider $provider, array $data): Provider
    {
        DB::transaction(function () use ($provider, $data) {
            $provider->update($data);
        });

        return $provider->fresh();
    }

    /**
     * Delete a provider.
     */
    public function deleteProvider(Provider $provider): bool
    {
        return $provider->delete();
    }

    /**
     * Get all active providers.
     */
    public function getActiveProviders(): Collection
    {
        return Provider::active()->orderBy('name', 'asc')->get();
    }

    /**
     * Activate a provider.
     */
    public function activateProvider(Provider $provider): Provider
    {
        DB::transaction(function () use ($provider) {
            $provider->activate();
        });

        return $provider->fresh();
    }

    /**
     * Deactivate a provider.
     */
    public function deactivateProvider(Provider $provider): Provider
    {
        DB::transaction(function () use ($provider) {
            $provider->deactivate();
        });

        return $provider->fresh();
    }
}
