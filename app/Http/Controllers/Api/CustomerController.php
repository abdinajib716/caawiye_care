<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends ApiController
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Customer::class);

        $filters = $request->only(['search', 'status', 'country_code', 'sort', 'direction']);
        $customers = $this->customerService->getPaginatedCustomers($filters, $request->get('per_page', 15));

        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return $this->successResponse(
            new CustomerResource($customer),
            __('Customer created successfully.'),
            201
        );
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        return $this->successResponse(
            new CustomerResource($customer),
            __('Customer retrieved successfully.')
        );
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $updatedCustomer = $this->customerService->updateCustomer($customer, $request->validated());

        return $this->successResponse(
            new CustomerResource($updatedCustomer),
            __('Customer updated successfully.')
        );
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $this->customerService->deleteCustomer($customer);

        return $this->successResponse(
            null,
            __('Customer deleted successfully.')
        );
    }

    /**
     * Get active customers for selection.
     */
    public function active(): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->customerService->getActiveCustomers();

        return $this->successResponse(
            CustomerResource::collection($customers),
            __('Active customers retrieved successfully.')
        );
    }

    /**
     * Get customer statistics.
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $statistics = $this->customerService->getCustomerStatistics();

        return $this->successResponse(
            $statistics,
            __('Customer statistics retrieved successfully.')
        );
    }

    /**
     * Search customers.
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $request->validate([
            'query' => 'required|string|min:2',
            'status' => 'nullable|in:active,inactive',
            'country_code' => 'nullable|string|max:5',
        ]);

        $customers = $this->customerService->searchCustomers(
            $request->query,
            $request->only(['status', 'country_code'])
        );

        return $this->successResponse(
            CustomerResource::collection($customers),
            __('Search results retrieved successfully.')
        );
    }

    /**
     * Bulk update customer status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $this->authorize('update', Customer::class);

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:customers,id',
            'status' => 'required|in:active,inactive',
        ]);

        $updatedCount = $this->customerService->bulkUpdateStatus($request->ids, $request->status);

        return $this->successResponse(
            ['updated_count' => $updatedCount],
            __(':count customers updated successfully.', ['count' => $updatedCount])
        );
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $this->authorize('delete', Customer::class);

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:customers,id',
        ]);

        $deletedCount = $this->customerService->bulkDeleteCustomers($request->ids);

        return $this->successResponse(
            ['deleted_count' => $deletedCount],
            __(':count customers deleted successfully.', ['count' => $deletedCount])
        );
    }

    /**
     * Get customers by country.
     */
    public function byCountry(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $request->validate([
            'country_code' => 'required|string|max:5',
        ]);

        $customers = $this->customerService->getCustomersByCountry($request->country_code);

        return $this->successResponse(
            CustomerResource::collection($customers),
            __('Customers retrieved successfully.')
        );
    }
}
