<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CustomerService
{
    /**
     * Get paginated customers with optional filters.
     */
    public function getPaginatedCustomers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply country code filter
        if (!empty($filters['country_code'])) {
            $query->where('country_code', $filters['country_code']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new customer.
     */
    public function createCustomer(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * Update an existing customer.
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }

    /**
     * Delete a customer (soft delete).
     */
    public function deleteCustomer(Customer $customer): bool
    {
        return $customer->delete();
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restoreCustomer(Customer $customer): bool
    {
        return $customer->restore();
    }

    /**
     * Force delete a customer (permanent deletion).
     */
    public function forceDeleteCustomer(Customer $customer): bool
    {
        return $customer->forceDelete();
    }

    /**
     * Get active customers for selection.
     */
    public function getActiveCustomers(): Collection
    {
        return Customer::active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Search customers by query.
     */
    public function searchCustomers(string $query, array $filters = []): Collection
    {
        $queryBuilder = Customer::search($query);

        // Apply additional filters
        if (!empty($filters['status'])) {
            $queryBuilder->where('status', $filters['status']);
        }

        if (!empty($filters['country_code'])) {
            $queryBuilder->where('country_code', $filters['country_code']);
        }

        return $queryBuilder->limit(50)->get();
    }

    /**
     * Get customer statistics.
     */
    public function getCustomerStatistics(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $inactiveCustomers = Customer::where('status', 'inactive')->count();
        
        // Get country distribution
        $countryDistribution = Customer::selectRaw('country_code, COUNT(*) as count')
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->pluck('count', 'country_code')
            ->toArray();

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'inactive_customers' => $inactiveCustomers,
            'country_distribution' => $countryDistribution,
        ];
    }

    /**
     * Bulk update customer status.
     */
    public function bulkUpdateStatus(array $customerIds, string $status): int
    {
        return Customer::whereIn('id', $customerIds)
            ->update(['status' => $status]);
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDeleteCustomers(array $customerIds): int
    {
        $customers = Customer::whereIn('id', $customerIds)->get();
        $deletedCount = 0;

        foreach ($customers as $customer) {
            if ($customer->delete()) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get customers by country code.
     */
    public function getCustomersByCountry(string $countryCode): Collection
    {
        return Customer::where('country_code', $countryCode)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get recently created customers.
     */
    public function getRecentCustomers(int $limit = 10): Collection
    {
        return Customer::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if phone number exists for another customer.
     */
    public function phoneExistsForOtherCustomer(string $phone, string $countryCode, ?int $excludeCustomerId = null): bool
    {
        $query = Customer::where('phone', $phone)
            ->where('country_code', $countryCode);

        if ($excludeCustomerId) {
            $query->where('id', '!=', $excludeCustomerId);
        }

        return $query->exists();
    }

    /**
     * Get available country codes with counts.
     */
    public function getCountryCodesWithCounts(): array
    {
        return Customer::selectRaw('country_code, COUNT(*) as count')
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'country_code')
            ->toArray();
    }
}
