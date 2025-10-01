<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ServiceService
{
    /**
     * Get paginated services with optional filters.
     */
    public function getPaginatedServices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Service::with('category');

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->inCategory($filters['category_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply featured filter
        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        // Apply price range filter
        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new service.
     */
    public function createService(array $data): Service
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $data['slug'] = $this->ensureUniqueSlug($data['slug']);

        return Service::create($data);
    }

    /**
     * Update an existing service.
     */
    public function updateService(Service $service, array $data): Service
    {
        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $service->name) {
            $newSlug = Str::slug($data['name']);
            if ($newSlug !== $service->slug) {
                $data['slug'] = $this->ensureUniqueSlug($newSlug, $service->id);
            }
        }

        $service->update($data);
        return $service->fresh();
    }

    /**
     * Delete a service (soft delete).
     */
    public function deleteService(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Restore a soft-deleted service.
     */
    public function restoreService(Service $service): bool
    {
        return $service->restore();
    }

    /**
     * Get active services for dropdown/selection.
     */
    public function getActiveServices(): Collection
    {
        return Service::active()
            ->orderBy('name')
            ->get(['id', 'name', 'price']);
    }

    /**
     * Get featured services.
     */
    public function getFeaturedServices(int $limit = 10): Collection
    {
        return Service::featured()
            ->active()
            ->with('category')
            ->limit($limit)
            ->get();
    }

    /**
     * Get services by category.
     */
    public function getServicesByCategory(ServiceCategory $category): Collection
    {
        return $category->services()
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get service statistics.
     */
    public function getServiceStatistics(): array
    {
        return [
            'total_services' => Service::count(),
            'active_services' => Service::active()->count(),
            'inactive_services' => Service::where('status', 'inactive')->count(),
            'discontinued_services' => Service::where('status', 'discontinued')->count(),
            'featured_services' => Service::featured()->count(),
            'average_price' => (float) Service::active()->avg('price'),
            'total_value' => (float) Service::active()->sum('price'),
        ];
    }

    /**
     * Bulk update service status.
     */
    public function bulkUpdateStatus(array $serviceIds, string $status): int
    {
        return Service::whereIn('id', $serviceIds)
            ->update(['status' => $status]);
    }

    /**
     * Bulk delete services.
     */
    public function bulkDelete(array $serviceIds): int
    {
        return Service::whereIn('id', $serviceIds)->delete();
    }

    /**
     * Search services with advanced filters.
     */
    public function searchServices(string $query, array $filters = []): Collection
    {
        $services = Service::search($query);

        if (!empty($filters['category_id'])) {
            $services->inCategory($filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $services->where('status', $filters['status']);
        }

        return $services->get();
    }

    /**
     * Ensure slug is unique.
     */
    private function ensureUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists.
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Service::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
