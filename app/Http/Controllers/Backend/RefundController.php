<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use App\Services\RefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function __construct(
        private readonly RefundService $refundService
    ) {
    }

    public function index(): View
    {
        $statistics = $this->refundService->getRefundStatistics();

        return view('backend.pages.refunds.index', [
            'statistics' => $statistics,
            'breadcrumbs' => [
                'title' => __('Refunds'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Refunds'), 'url' => null],
                ],
            ],
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $orderType = $request->get('order_type', Order::class);
        $orderId = $request->get('order_id');

        $order = null;
        if ($orderId && class_exists($orderType)) {
            $order = $orderType::find($orderId);
        }

        if ($order) {
            $activeRefund = Refund::query()
                ->where('order_type', $orderType)
                ->where('order_id', $orderId)
                ->whereNotIn('status', ['rejected'])
                ->latest()
                ->first();

            if ($activeRefund) {
                return redirect()
                    ->route('admin.refunds.show', $activeRefund)
                    ->with('info', __('A refund request already exists for this order.'));
            }

            if (method_exists($order, 'canBeRefunded') && ! $order->canBeRefunded()) {
                return redirect()
                    ->route('admin.orders.show', $order)
                    ->with('error', __('This order is not eligible for a refund.'));
            }
        }

        return view('backend.pages.refunds.create', [
            'order' => $order,
            'orderType' => $orderType,
            'breadcrumbs' => [
                'title' => __('Initiate Refund'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Refunds'), 'url' => route('admin.refunds.index')],
                    ['label' => __('Initiate'), 'url' => null],
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_type' => 'required|string',
            'order_id' => 'required|integer',
            'refund_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $refund = $this->refundService->initiateRefund(
                $validated['order_type'],
                $validated['order_id'],
                (float) $validated['refund_amount'],
                $validated['reason']
            );

            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Refund request initiated successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Refund $refund): View
    {
        $refund->load(['requestedBy', 'approvedBy', 'processedBy', 'revenueReversal']);

        $order = null;
        if (class_exists($refund->order_type)) {
            $order = $refund->order_type::find($refund->order_id);
        }

        return view('backend.pages.refunds.show', [
            'refund' => $refund,
            'order' => $order,
            'breadcrumbs' => [
                'title' => __('Refund #:number', ['number' => $refund->refund_number]),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Refunds'), 'url' => route('admin.refunds.index')],
                    ['label' => $refund->refund_number, 'url' => null],
                ],
            ],
        ]);
    }

    public function confirmProviderReversed(Refund $refund): RedirectResponse
    {
        try {
            $this->refundService->confirmProviderPaymentReversed($refund);
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Provider payment reversal confirmed.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function approve(Refund $refund): RedirectResponse
    {
        try {
            $this->refundService->approveRefund($refund);
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Refund approved successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Refund $refund): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $this->refundService->rejectRefund($refund, $request->input('rejection_reason'));
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Refund rejected.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function process(Refund $refund): RedirectResponse
    {
        try {
            $this->refundService->processRefund($refund);
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Refund is now being processed.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, Refund $refund): RedirectResponse
    {
        $validated = $request->validate([
            'refund_method' => 'required|in:evc,edahab,cash,bank',
            'refund_reference' => 'nullable|string|max:255',
        ]);

        try {
            $this->refundService->completeRefund(
                $refund,
                $validated['refund_method'],
                $validated['refund_reference'] ?? null
            );
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', __('Refund completed successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
