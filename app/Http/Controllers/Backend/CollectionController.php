<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReportCollection;
use App\Services\ReportCollectionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionController extends Controller
{
    protected ReportCollectionService $reportCollectionService;

    public function __construct(ReportCollectionService $reportCollectionService)
    {
        $this->reportCollectionService = $reportCollectionService;
    }

    public function index(): View
    {
        return view('backend.pages.collections.index', [
            'breadcrumbs' => [
                'title' => __('Report Collections'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Collections'), 'url' => null],
                ],
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.collections.create', [
            'breadcrumbs' => [
                'title' => __('New Report Collection'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Collections'), 'url' => route('admin.collections.index')],
                    ['label' => __('New Request'), 'url' => null],
                ],
            ],
        ]);
    }

    public function show(int $id): View
    {
        $collection = ReportCollection::with([
            'assignedStaff',
            'creator',
            'logs.performer',
            'pickupLocation',
            'dropoffLocation',
            'medicineOrder.supplier',
            'medicineOrder.pickupLocation',
            'medicineOrder.dropoffLocation',
        ])
            ->findOrFail($id);

        return view('backend.pages.collections.show', [
            'collection' => $collection,
            'breadcrumbs' => [
                'title' => __('Request Details'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Collections'), 'url' => route('admin.collections.index')],
                    ['label' => $collection->request_id, 'url' => null],
                ],
            ],
        ]);
    }

    public function settings(): View
    {
        $charges = $this->reportCollectionService->getServiceCharges();

        return view('backend.pages.collections.settings', [
            'charges' => $charges,
            'breadcrumbs' => [
                'title' => __('Service Charges'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Collections'), 'url' => route('admin.collections.index')],
                    ['label' => __('Settings'), 'url' => null],
                ],
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'service_charge' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        update_setting('report_collection_service_charge', $validated['service_charge']);
        update_setting('report_collection_delivery_fee', $validated['delivery_fee']);

        return redirect()->route('admin.collections.settings')
            ->with('success', __('Service charges updated successfully.'));
    }
}
