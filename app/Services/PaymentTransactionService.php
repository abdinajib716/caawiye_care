<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaymentTransactionService
{
    /**
     * Get paginated transactions with optional filters.
     */
    public function getPaginatedTransactions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PaymentTransaction::with(['customer', 'order']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('transaction_id', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('reference_id', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('customer_phone', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('customer_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('order', function ($orderQuery) use ($filters) {
                        $orderQuery->where('order_number', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply payment method filter
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Apply provider filter
        if (!empty($filters['provider'])) {
            $query->where('provider', $filters['provider']);
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get transaction statistics.
     */
    public function getTransactionStatistics(): array
    {
        return [
            'total_transactions' => PaymentTransaction::count(),
            'successful_transactions' => PaymentTransaction::completed()->count(),
            'failed_transactions' => PaymentTransaction::failed()->count(),
            'pending_transactions' => PaymentTransaction::pending()->count(),
            'total_amount' => (float) PaymentTransaction::completed()->sum('amount'),
            'today_transactions' => PaymentTransaction::whereDate('created_at', today())->count(),
            'today_amount' => (float) PaymentTransaction::whereDate('created_at', today())->where('status', 'completed')->sum('amount'),
        ];
    }

    /**
     * Get transaction by reference ID.
     */
    public function getTransactionByReference(string $referenceId): ?PaymentTransaction
    {
        return PaymentTransaction::where('reference_id', $referenceId)->first();
    }

    /**
     * Get transaction by transaction ID.
     */
    public function getTransactionById(string $transactionId): ?PaymentTransaction
    {
        return PaymentTransaction::where('transaction_id', $transactionId)->first();
    }

    /**
     * Create a new transaction.
     */
    public function createTransaction(array $data): PaymentTransaction
    {
        return PaymentTransaction::create($data);
    }

    /**
     * Update transaction status.
     */
    public function updateTransactionStatus(PaymentTransaction $transaction, string $status, array $additionalData = []): bool
    {
        $updateData = array_merge(['status' => $status], $additionalData);

        if ($status === 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($status === 'failed') {
            $updateData['failed_at'] = now();
        } elseif ($status === 'processing') {
            $updateData['processed_at'] = now();
        }

        return $transaction->update($updateData);
    }

    /**
     * Get recent transactions.
     */
    public function getRecentTransactions(int $limit = 10): Collection
    {
        return PaymentTransaction::with(['customer', 'order'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get transactions by customer.
     */
    public function getCustomerTransactions(int $customerId, int $limit = null): Collection|LengthAwarePaginator
    {
        $query = PaymentTransaction::where('customer_id', $customerId)
            ->with('order')
            ->latest();

        return $limit ? $query->limit($limit)->get() : $query->paginate(15);
    }

    /**
     * Get transactions by status.
     */
    public function getTransactionsByStatus(string $status, int $limit = null): Collection
    {
        $query = PaymentTransaction::where('status', $status)
            ->with(['customer', 'order'])
            ->latest();

        return $limit ? $query->limit($limit)->get() : $query->get();
    }

    /**
     * Check if transaction can be retried.
     */
    public function canRetry(PaymentTransaction $transaction): bool
    {
        return in_array($transaction->status, ['failed', 'expired', 'cancelled']);
    }

    /**
     * Mark transaction as expired.
     */
    public function markAsExpired(PaymentTransaction $transaction): bool
    {
        return $transaction->update([
            'status' => 'expired',
            'failed_at' => now(),
            'error_message' => 'Transaction expired',
        ]);
    }

    /**
     * Get transaction summary for a date range.
     */
    public function getTransactionSummary(string $startDate, string $endDate): array
    {
        $transactions = PaymentTransaction::whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'total_count' => $transactions->count(),
            'completed_count' => $transactions->where('status', 'completed')->count(),
            'failed_count' => $transactions->where('status', 'failed')->count(),
            'pending_count' => $transactions->where('status', 'pending')->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'average_amount' => $transactions->where('status', 'completed')->avg('amount'),
        ];
    }
}

