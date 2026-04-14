<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Refund;
use App\Models\Order;
use App\Models\ProviderPayment;
use App\Models\RevenueLedger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RefundService
{
    public function __construct(
        private RevenueLedgerService $revenueLedgerService
    ) {
    }

    public function getPaginatedRefunds(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Refund::with(['requestedBy', 'approvedBy', 'processedBy']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function initiateRefund(string $orderType, int $orderId, float $refundAmount, string $reason): Refund
    {
        $order = $this->getOrder($orderType, $orderId);

        $this->validateRefundRequest($order, $refundAmount);

        return DB::transaction(function () use ($order, $orderType, $orderId, $refundAmount, $reason) {
            $providerPaymentReversed = $this->checkProviderPaymentStatus($orderType, $orderId);

            $refund = Refund::create([
                'refund_number' => $this->generateRefundNumber(),
                'order_type' => $orderType,
                'order_id' => $orderId,
                'original_amount' => $order->total,
                'refund_amount' => $refundAmount,
                'reason' => $reason,
                'status' => 'pending',
                'provider_payment_reversed' => $providerPaymentReversed,
                'requested_by' => Auth::id(),
            ]);

            return $refund;
        });
    }

    public function approveRefund(Refund $refund): Refund
    {
        if (!$refund->isPending()) {
            throw new \Exception('Only pending refunds can be approved.');
        }

        if (!$refund->provider_payment_reversed) {
            throw new \Exception('Refund not allowed until provider payment is reversed.');
        }

        return DB::transaction(function () use ($refund) {
            $refund->approve(Auth::id());

            $revenueReversal = $this->revenueLedgerService->reverseRevenue(
                $refund->order_type,
                $refund->order_id,
                (float) $refund->refund_amount,
                $refund->id
            );

            $refund->update(['revenue_reversal_id' => $revenueReversal->id]);

            return $refund->fresh();
        });
    }

    public function rejectRefund(Refund $refund, string $reason): Refund
    {
        if (!$refund->isPending()) {
            throw new \Exception('Only pending refunds can be rejected.');
        }

        $refund->reject(Auth::id(), $reason);
        return $refund->fresh();
    }

    public function processRefund(Refund $refund): Refund
    {
        if (!$refund->isApproved()) {
            throw new \Exception('Only approved refunds can be processed.');
        }

        $refund->markAsProcessing(Auth::id());
        return $refund->fresh();
    }

    public function completeRefund(Refund $refund, string $method, ?string $reference = null): Refund
    {
        if (!$refund->isProcessing()) {
            throw new \Exception('Only processing refunds can be completed.');
        }

        return DB::transaction(function () use ($refund, $method, $reference) {
            $refund->complete($method, $reference);

            $order = $this->getOrder($refund->order_type, $refund->order_id);
            $order->markAsRefunded($refund->reason);

            return $refund->fresh();
        });
    }

    public function confirmProviderPaymentReversed(Refund $refund): Refund
    {
        if ($refund->provider_payment_reversed) {
            throw new \Exception('Provider payment already confirmed as reversed.');
        }

        $refund->confirmProviderPaymentReversed();

        $providerPayments = ProviderPayment::where('order_type', $refund->order_type)
            ->where('order_id', $refund->order_id)
            ->get();

        foreach ($providerPayments as $payment) {
            if ($payment->canBeReversed()) {
                $payment->reverse(Auth::id(), "Reversed for refund #{$refund->refund_number}");
            }
        }

        return $refund->fresh();
    }

    public function getRefundStatistics(): array
    {
        return [
            'total_refunds' => Refund::count(),
            'pending_count' => Refund::pending()->count(),
            'approved_count' => Refund::approved()->count(),
            'processing_count' => Refund::processing()->count(),
            'completed_count' => Refund::completed()->count(),
            'rejected_count' => Refund::rejected()->count(),
            'total_refunded_amount' => (float) Refund::completed()->sum('refund_amount'),
            'pending_refund_amount' => (float) Refund::whereIn('status', ['pending', 'approved', 'processing'])->sum('refund_amount'),
        ];
    }

    private function validateRefundRequest($order, float $refundAmount): void
    {
        if (!method_exists($order, 'isCompleted') || !$order->isCompleted()) {
            throw new \Exception('Only completed orders can be refunded.');
        }

        if (method_exists($order, 'isRefunded') && $order->isRefunded()) {
            throw new \Exception('This order has already been refunded.');
        }

        if ($refundAmount > (float) $order->total) {
            throw new \Exception('Refund amount cannot exceed the order total.');
        }

        if ($refundAmount <= 0) {
            throw new \Exception('Refund amount must be greater than zero.');
        }

        $existingRefund = Refund::where('order_type', get_class($order))
            ->where('order_id', $order->id)
            ->whereNotIn('status', ['rejected'])
            ->first();

        if ($existingRefund) {
            throw new \Exception('A refund request already exists for this order.');
        }
    }

    private function checkProviderPaymentStatus(string $orderType, int $orderId): bool
    {
        $providerPayments = ProviderPayment::where('order_type', $orderType)
            ->where('order_id', $orderId)
            ->get();

        if ($providerPayments->isEmpty()) {
            return true;
        }

        foreach ($providerPayments as $payment) {
            if ($payment->isPaid() && !$payment->isReversed()) {
                return false;
            }
        }

        return true;
    }

    private function getOrder(string $orderType, int $orderId)
    {
        $modelClass = $orderType;

        if (!class_exists($modelClass)) {
            throw new \Exception("Invalid order type: {$orderType}");
        }

        $order = $modelClass::find($orderId);

        if (!$order) {
            throw new \Exception('Order not found.');
        }

        return $order;
    }

    public function generateRefundNumber(): string
    {
        $prefix = 'REF';
        $date = now()->format('Ymd');
        $lastRefund = Refund::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRefund ? (int) substr($lastRefund->refund_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
