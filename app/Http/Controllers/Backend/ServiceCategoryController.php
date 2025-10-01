<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategory\StoreServiceCategoryRequest;
use App\Http\Requests\ServiceCategory\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use App\Services\ServiceCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function __construct(
        private readonly ServiceCategoryService $serviceCategoryService
    ) {
    }

    /**
     * Display a listing of service categories.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ServiceCategory::class);

        return view('backend.pages.service-categories.index', [
            'breadcrumbs' => [
                'title' => __('Service Categories'),
                'items' => [],
            ],
        ]);
    }

    /**
     * Show the form for creating a new service category.
     */
    public function create(): View
    {
        $this->authorize('create', ServiceCategory::class);

        return view('backend.pages.service-categories.create', [
            'breadcrumbs' => [
                'title' => __('Create Category'),
                'items' => [
                    ['label' => __('Service Categories'), 'url' => route('admin.service-categories.index')],
                    ['label' => __('Create'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created service category in storage.
     */
    public function store(StoreServiceCategoryRequest $request): RedirectResponse
    {
        $category = $this->serviceCategoryService->createCategory($request->validated());

        return redirect()
            ->route('admin.service-categories.index')
            ->with('success', __('Category created successfully.'));
    }

    /**
     * Display the specified service category.
     */
    public function show(ServiceCategory $serviceCategory): View
    {
        $this->authorize('view', $serviceCategory);

        return view('backend.pages.service-categories.show', [
            'category' => $serviceCategory->load(['children', 'services']),
            'breadcrumbs' => [
                'title' => $serviceCategory->name,
                'items' => [
                    ['label' => __('Service Categories'), 'url' => route('admin.service-categories.index')],
                    ['label' => $serviceCategory->name, 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified service category.
     */
    public function edit(ServiceCategory $serviceCategory): View
    {
        $this->authorize('update', $serviceCategory);

        return view('backend.pages.service-categories.edit', [
            'category' => $serviceCategory,
            'breadcrumbs' => [
                'title' => __('Edit Category'),
                'items' => [
                    ['label' => __('Service Categories'), 'url' => route('admin.service-categories.index')],
                    ['label' => $serviceCategory->name, 'url' => route('admin.service-categories.show', $serviceCategory)],
                    ['label' => __('Edit'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Update the specified service category in storage.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->serviceCategoryService->updateCategory($serviceCategory, $request->validated());

        return redirect()
            ->route('admin.service-categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    /**
     * Remove the specified service category from storage.
     */
    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->authorize('delete', $serviceCategory);

        // Check if category has services
        if ($serviceCategory->services()->count() > 0) {
            return redirect()
                ->route('admin.service-categories.index')
                ->with('error', __('Cannot delete category that contains services. Please move or delete the services first.'));
        }

        // Check if category has child categories
        if ($serviceCategory->children()->count() > 0) {
            return redirect()
                ->route('admin.service-categories.index')
                ->with('error', __('Cannot delete category that has subcategories. Please delete the subcategories first.'));
        }

        $this->serviceCategoryService->deleteCategory($serviceCategory);

        return redirect()
            ->route('admin.service-categories.index')
            ->with('success', __('Category deleted successfully.'));
    }

    /**
     * Bulk delete service categories.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', ServiceCategory::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:service_categories,id',
        ]);

        $deletedCount = $this->serviceCategoryService->bulkDeleteCategories($request->ids);

        return redirect()
            ->route('admin.service-categories.index')
            ->with('success', __(':count categories deleted successfully.', ['count' => $deletedCount]));
    }
}
