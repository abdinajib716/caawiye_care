<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        // Get order statistics
        $statistics = $this->orderService->getOrderStatistics();

        return view('backend.pages.orders.index', [
            'statistics' => $statistics,
            'breadcrumbs' => [
                'title' => __('Orders'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Orders'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['customer', 'agent', 'items.service', 'paymentTransaction']);

        return view('backend.pages.orders.show', [
            'order' => $order,
            'breadcrumbs' => [
                'title' => __('Order #:number', ['number' => $order->order_number]),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Orders'), 'url' => route('admin.orders.index')],
                    ['label' => $order->order_number, 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Update the specified order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,failed',
            'notes' => 'nullable|string',
        ]);

        $this->orderService->updateOrder($order, $request->only(['status', 'notes']));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', __('Order status updated successfully.'));
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->orderService->cancelOrder($order, $request->input('reason'));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', __('Order cancelled successfully.'));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $this->orderService->deleteOrder($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', __('Order deleted successfully.'));
    }

    /**
     * Bulk delete orders.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('delete', Order::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);

        $count = $this->orderService->bulkDelete($request->input('ids'));

        return redirect()
            ->route('admin.orders.index')
            ->with('success', __(':count orders deleted successfully.', ['count' => $count]));
    }

    /**
     * Bulk update order status.
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $this->authorize('update', Order::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,completed,cancelled,failed',
        ]);

        $count = $this->orderService->bulkUpdateStatus(
            $request->input('ids'),
            $request->input('status')
        );

        return redirect()
            ->route('admin.orders.index')
            ->with('success', __(':count orders updated successfully.', ['count' => $count]));
    }
}

