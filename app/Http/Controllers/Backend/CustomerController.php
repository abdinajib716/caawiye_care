<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        // Get customer statistics
        $statistics = $this->customerService->getCustomerStatistics();

        return view('backend.pages.customers.index', [
            'statistics' => $statistics,
            'breadcrumbs' => [
                'title' => __('Customers'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Customers'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('backend.pages.customers.create', [
            'breadcrumbs' => [
                'title' => __('Create Customer'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Customers'), 'url' => route('admin.customers.index')],
                    ['label' => __('Create'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return redirect()
            ->route('admin.customers.index')
            ->with('success', __('Customer created successfully.'));
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        return view('backend.pages.customers.show', [
            'customer' => $customer,
            'breadcrumbs' => [
                'title' => $customer->name,
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Customers'), 'url' => route('admin.customers.index')],
                    ['label' => $customer->name, 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('backend.pages.customers.edit', [
            'customer' => $customer,
            'breadcrumbs' => [
                'title' => __('Edit Customer'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Customers'), 'url' => route('admin.customers.index')],
                    ['label' => $customer->name, 'url' => route('admin.customers.show', $customer)],
                    ['label' => __('Edit'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $updatedCustomer = $this->customerService->updateCustomer($customer, $request->validated());

        return redirect()
            ->route('admin.customers.show', $updatedCustomer)
            ->with('success', __('Customer updated successfully.'));
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $this->customerService->deleteCustomer($customer);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', __('Customer deleted successfully.'));
    }

    /**
     * Bulk delete customers.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', Customer::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
        ]);

        $deletedCount = $this->customerService->bulkDeleteCustomers($request->ids);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', __(':count customers deleted successfully.', ['count' => $deletedCount]));
    }

    /**
     * Bulk update customer status.
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $this->authorize('update', Customer::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customers,id',
            'status' => 'required|in:active,inactive',
        ]);

        $updatedCount = $this->customerService->bulkUpdateStatus($request->ids, $request->status);

        $statusLabel = $request->status === 'active' ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.customers.index')
            ->with('success', __(':count customers :status successfully.', [
                'count' => $updatedCount,
                'status' => $statusLabel
            ]));
    }
}
