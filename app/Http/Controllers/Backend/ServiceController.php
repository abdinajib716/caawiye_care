<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService
    ) {
    }

    /**
     * Display a listing of services.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Service::class);

        $filters = $request->only(['search', 'category_id', 'status', 'is_featured', 'price_min', 'price_max', 'sort', 'direction']);
        $services = $this->serviceService->getPaginatedServices($filters, $request->get('per_page', 15));
        $categories = ServiceCategory::active()->orderBy('name')->get(['id', 'name']);
        $statistics = $this->serviceService->getServiceStatistics();

        return view('backend.pages.services.index', [
            'services' => $services,
            'categories' => $categories,
            'statistics' => $statistics,
            'filters' => $filters,
            'breadcrumbs' => [
                'title' => __('Services'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Services'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $this->authorize('create', Service::class);

        $categories = ServiceCategory::active()->orderBy('name')->get(['id', 'name']);

        return view('backend.pages.services.create', [
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Create Service'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Services'), 'url' => route('admin.services.index')],
                    ['label' => __('Create'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = $this->serviceService->createService($request->validated());

        return redirect()
            ->route('admin.services.index')
            ->with('success', __('Service created successfully.'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        $this->authorize('view', $service);

        return view('backend.pages.services.show', [
            'service' => $service->load('category'),
            'breadcrumbs' => [
                'title' => $service->name,
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Services'), 'url' => route('admin.services.index')],
                    ['label' => $service->name, 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        $categories = ServiceCategory::active()->orderBy('name')->get(['id', 'name']);

        return view('backend.pages.services.edit', [
            'service' => $service,
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Edit Service'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Services'), 'url' => route('admin.services.index')],
                    ['label' => __('Edit'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $this->serviceService->updateService($service, $request->validated());

        return redirect()
            ->route('admin.services.index')
            ->with('success', __('Service updated successfully.'));
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        $this->serviceService->deleteService($service);

        return redirect()
            ->route('admin.services.index')
            ->with('success', __('Service deleted successfully.'));
    }

    /**
     * Bulk delete services.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', Service::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        $count = $this->serviceService->bulkDelete($request->ids);

        return redirect()
            ->route('admin.services.index')
            ->with('success', __(':count services deleted successfully.', ['count' => $count]));
    }

    /**
     * Bulk update service status.
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $this->authorize('update', Service::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $count = $this->serviceService->bulkUpdateStatus($request->ids, $request->status);

        return redirect()
            ->route('admin.services.index')
            ->with('success', __(':count services updated successfully.', ['count' => $count]));
    }
}
