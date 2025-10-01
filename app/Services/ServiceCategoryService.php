<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceCategoryService
{
    /**
     * Get paginated categories with optional filters.
     */
    public function getPaginatedCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ServiceCategory::with(['parent', 'children'])
            ->withCount(['services', 'activeServices']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Apply parent filter
        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'root') {
                $query->root();
            } elseif (!empty($filters['parent_id'])) {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        // Apply status filter
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'sort_order';
        $sortDirection = $filters['direction'] ?? 'asc';
        
        if ($sortField === 'services_count') {
            $query->orderBy('services_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): ServiceCategory
    {
        // Set default sort order if not provided
        if (!isset($data['sort_order'])) {
            $maxOrder = ServiceCategory::where('parent_id', $data['parent_id'] ?? null)
                ->max('sort_order');
            $data['sort_order'] = ($maxOrder ?? 0) + 1;
        }

        return ServiceCategory::create($data);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(ServiceCategory $category, array $data): ServiceCategory
    {
        // Prevent setting parent to self or descendant
        if (isset($data['parent_id']) && $data['parent_id']) {
            if ($data['parent_id'] == $category->id) {
                throw new \InvalidArgumentException('Category cannot be its own parent.');
            }

            if ($this->isDescendant($category, $data['parent_id'])) {
                throw new \InvalidArgumentException('Category cannot be moved to its own descendant.');
            }
        }

        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(ServiceCategory $category): bool
    {
        // Check if category has services
        if ($category->services()->count() > 0) {
            throw new \InvalidArgumentException('Cannot delete category that contains services.');
        }

        // Check if category has child categories
        if ($category->children()->count() > 0) {
            throw new \InvalidArgumentException('Cannot delete category that has subcategories.');
        }

        return $category->delete();
    }

    /**
     * Bulk delete categories.
     */
    public function bulkDeleteCategories(array $categoryIds): int
    {
        $categories = ServiceCategory::whereIn('id', $categoryIds)->get();
        $deletedCount = 0;

        foreach ($categories as $category) {
            try {
                if ($this->deleteCategory($category)) {
                    $deletedCount++;
                }
            } catch (\InvalidArgumentException $e) {
                // Skip categories that cannot be deleted
                continue;
            }
        }

        return $deletedCount;
    }

    /**
     * Get category tree structure.
     */
    public function getCategoryTree(): Collection
    {
        $categories = ServiceCategory::with(['children' => function ($query) {
            $query->orderBy('sort_order');
        }])
        ->root()
        ->orderBy('sort_order')
        ->get();

        return $categories;
    }

    /**
     * Get categories for dropdown/select options.
     */
    public function getCategoriesForSelect(bool $includeInactive = false): Collection
    {
        $query = ServiceCategory::orderBy('name');

        if (!$includeInactive) {
            $query->active();
        }

        return $query->get(['id', 'name', 'parent_id'])
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->full_path,
                ];
            });
    }

    /**
     * Get category statistics.
     */
    public function getCategoryStatistics(): array
    {
        return [
            'total_categories' => ServiceCategory::count(),
            'active_categories' => ServiceCategory::active()->count(),
            'inactive_categories' => ServiceCategory::where('is_active', false)->count(),
            'root_categories' => ServiceCategory::root()->count(),
            'categories_with_services' => ServiceCategory::has('services')->count(),
            'empty_categories' => ServiceCategory::doesntHave('services')->count(),
        ];
    }

    /**
     * Reorder categories.
     */
    public function reorderCategories(array $categoryOrders): void
    {
        foreach ($categoryOrders as $order) {
            ServiceCategory::where('id', $order['id'])
                ->update(['sort_order' => $order['sort_order']]);
        }
    }

    /**
     * Check if a category is a descendant of another category.
     */
    private function isDescendant(ServiceCategory $category, int $potentialAncestorId): bool
    {
        $descendants = $this->getAllDescendants($category);
        return $descendants->contains('id', $potentialAncestorId);
    }

    /**
     * Get all descendants of a category.
     */
    private function getAllDescendants(ServiceCategory $category): Collection
    {
        $descendants = collect();
        
        foreach ($category->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($this->getAllDescendants($child));
        }

        return $descendants;
    }

    /**
     * Move category to different parent.
     */
    public function moveCategory(ServiceCategory $category, ?int $newParentId): ServiceCategory
    {
        if ($newParentId && $this->isDescendant($category, $newParentId)) {
            throw new \InvalidArgumentException('Category cannot be moved to its own descendant.');
        }

        // Update sort order to be last in new parent
        $maxOrder = ServiceCategory::where('parent_id', $newParentId)->max('sort_order');
        
        $category->update([
            'parent_id' => $newParentId,
            'sort_order' => ($maxOrder ?? 0) + 1,
        ]);

        return $category->fresh();
    }
}
