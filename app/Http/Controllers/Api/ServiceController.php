<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends ApiController
{
    public function __construct(
        private readonly ServiceService $serviceService
    ) {
    }

    /**
     * Display a listing of services.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Service::class);

        $filters = $request->only(['search', 'category_id', 'status', 'is_featured', 'price_min', 'price_max', 'sort', 'direction']);
        $services = $this->serviceService->getPaginatedServices($filters, $request->get('per_page', 15));

        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->serviceService->createService($request->validated());

        return $this->successResponse(
            new ServiceResource($service->load('category')),
            __('Service created successfully.'),
            201
        );
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): JsonResponse
    {
        $this->authorize('view', $service);

        return $this->successResponse(
            new ServiceResource($service->load('category')),
            __('Service retrieved successfully.')
        );
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $updatedService = $this->serviceService->updateService($service, $request->validated());

        return $this->successResponse(
            new ServiceResource($updatedService->load('category')),
            __('Service updated successfully.')
        );
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): JsonResponse
    {
        $this->authorize('delete', $service);

        $this->serviceService->deleteService($service);

        return $this->successResponse(
            null,
            __('Service deleted successfully.')
        );
    }

    /**
     * Get active services for selection.
     */
    public function active(): JsonResponse
    {
        $this->authorize('viewAny', Service::class);

        $services = $this->serviceService->getActiveServices();

        return $this->successResponse(
            ServiceResource::collection($services),
            __('Active services retrieved successfully.')
        );
    }

    /**
     * Get featured services.
     */
    public function featured(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Service::class);

        $limit = $request->get('limit', 10);
        $services = $this->serviceService->getFeaturedServices($limit);

        return $this->successResponse(
            ServiceResource::collection($services),
            __('Featured services retrieved successfully.')
        );
    }

    /**
     * Get service statistics.
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Service::class);

        $statistics = $this->serviceService->getServiceStatistics();

        return $this->successResponse(
            $statistics,
            __('Service statistics retrieved successfully.')
        );
    }

    /**
     * Search services.
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Service::class);

        $request->validate([
            'query' => 'required|string|min:2',
            'category_id' => 'nullable|exists:service_categories,id',
            'status' => 'nullable|in:active,inactive,discontinued',
        ]);

        $services = $this->serviceService->searchServices(
            $request->query,
            $request->only(['category_id', 'status'])
        );

        return $this->successResponse(
            ServiceResource::collection($services),
            __('Search results retrieved successfully.')
        );
    }

    /**
     * Bulk update service status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $this->authorize('update', Service::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        $count = $this->serviceService->bulkUpdateStatus($request->ids, $request->status);

        return $this->successResponse(
            ['updated_count' => $count],
            __(':count services updated successfully.', ['count' => $count])
        );
    }

    /**
     * Bulk delete services.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $this->authorize('delete', Service::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:services,id',
        ]);

        $count = $this->serviceService->bulkDelete($request->ids);

        return $this->successResponse(
            ['deleted_count' => $count],
            __(':count services deleted successfully.', ['count' => $count])
        );
    }
}
