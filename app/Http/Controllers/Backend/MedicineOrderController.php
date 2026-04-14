<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineOrderController extends Controller
{
    public function index(): View
    {
        $statistics = [
            'total_orders' => MedicineOrder::count(),
            'pending_orders' => MedicineOrder::where('status', 'pending')->count(),
            'in_office_orders' => MedicineOrder::where('status', 'in_office')->count(),
            'delivered_orders' => MedicineOrder::where('status', 'delivered')->count(),
            'cancelled_orders' => MedicineOrder::where('status', 'cancelled')->count(),
            'total_revenue' => MedicineOrder::where('payment_status', 'completed')->sum('total'),
        ];

        return view('backend.pages.medicine-orders.index', [
            'title' => __('Medicine Orders'),
            'statistics' => $statistics,
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicine Orders'), 'url' => null],
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.medicine-orders.create', [
            'title' => __('Book Medicine Collection'),
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicine Orders'), 'url' => route('admin.medicine-orders.index')],
                ['label' => __('Book Medicine'), 'url' => null],
            ],
        ]);
    }

    public function show(MedicineOrder $medicineOrder): View
    {
        $medicineOrder->load(['customer', 'supplier', 'agent', 'items.medicine', 'pickupLocation', 'dropoffLocation']);

        return view('backend.pages.medicine-orders.show', [
            'title' => __('Medicine Order #:number', ['number' => $medicineOrder->order_number]),
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicine Orders'), 'url' => route('admin.medicine-orders.index')],
                ['label' => $medicineOrder->order_number, 'url' => null],
            ],
            'order' => $medicineOrder,
        ]);
    }

    public function updateStatus(Request $request, MedicineOrder $medicineOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_office,delivered,cancelled'],
        ]);

        $medicineOrder->update(['status' => $validated['status']]);

        return redirect()
            ->route('admin.medicine-orders.show', $medicineOrder)
            ->with('success', __('Medicine booking status updated successfully.'));
    }
}
