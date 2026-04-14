<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ProviderPayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    public function getPaginatedExpenses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Expense::with(['category', 'createdBy', 'approvedBy']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['transaction_method'])) {
            $query->where('transaction_method', $filters['transaction_method']);
        }

        $sortField = $filters['sort'] ?? 'expense_date';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $data['expense_number'] = $this->generateExpenseNumber();
            $data['created_by'] = Auth::id();

            return Expense::create($data);
        });
    }

    public function updateExpense(Expense $expense, array $data): Expense
    {
        if (!$expense->canBeEdited()) {
            throw new \Exception('This expense cannot be edited.');
        }

        $expense->update($data);
        return $expense->fresh();
    }

    public function deleteExpense(Expense $expense): bool
    {
        if (!$expense->canBeEdited()) {
            throw new \Exception('This expense cannot be deleted.');
        }

        return $expense->delete();
    }

    public function submitForApproval(Expense $expense): Expense
    {
        if (!$expense->isDraft() && !$expense->isRejected()) {
            throw new \Exception('Only draft or rejected expenses can be submitted for approval.');
        }

        $expense->submitForApproval();
        return $expense->fresh();
    }

    public function approveExpense(Expense $expense): Expense
    {
        if (!$expense->canBeApproved()) {
            throw new \Exception('This expense cannot be approved.');
        }

        $expense->approve(Auth::id());
        return $expense->fresh();
    }

    public function rejectExpense(Expense $expense, string $reason): Expense
    {
        if (!$expense->canBeApproved()) {
            throw new \Exception('This expense cannot be rejected.');
        }

        $expense->reject(Auth::id(), $reason);
        return $expense->fresh();
    }

    public function markAsPaid(Expense $expense): Expense
    {
        if (!$expense->canBePaid()) {
            throw new \Exception('This expense cannot be marked as paid.');
        }

        $expense->markAsPaid();
        return $expense->fresh();
    }

    public function createProviderExpense(
        string $orderType,
        int $orderId,
        int $providerId,
        string $providerType,
        float $amount,
        ?string $description = null
    ): Expense {
        $providerCategory = ExpenseCategory::where('slug', 'provider-transactions')->first();

        if (!$providerCategory) {
            $providerCategory = ExpenseCategory::create([
                'name' => 'Provider Transactions',
                'slug' => 'provider-transactions',
                'description' => 'Payments to service providers',
                'is_system' => true,
                'is_active' => true,
            ]);
        }

        return DB::transaction(function () use ($providerCategory, $orderType, $orderId, $providerId, $providerType, $amount, $description) {
            $expense = $this->createExpense([
                'expense_date' => now()->toDateString(),
                'category_id' => $providerCategory->id,
                'description' => $description ?? 'Provider payment for order',
                'amount' => $amount,
                'transaction_method' => 'evc',
                'payee_type' => $providerType,
                'payee_id' => $providerId,
                'related_order_type' => $orderType,
                'related_order_id' => $orderId,
                'status' => 'pending_approval',
            ]);

            ProviderPayment::create([
                'payment_number' => $this->generateProviderPaymentNumber(),
                'order_type' => $orderType,
                'order_id' => $orderId,
                'provider_type' => $providerType,
                'provider_id' => $providerId,
                'amount' => $amount,
                'status' => 'pending',
                'expense_id' => $expense->id,
            ]);

            return $expense;
        });
    }

    public function getExpenseStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Expense::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $approvedQuery = clone $query;
        $approvedQuery->whereIn('status', ['approved', 'paid']);

        return [
            'total_expenses' => $query->count(),
            'draft_count' => (clone $query)->draft()->count(),
            'pending_approval_count' => (clone $query)->pendingApproval()->count(),
            'approved_count' => (clone $query)->approved()->count(),
            'paid_count' => (clone $query)->paid()->count(),
            'total_amount' => (float) $approvedQuery->sum('amount'),
            'paid_amount' => (float) (clone $query)->paid()->sum('amount'),
        ];
    }

    public function getExpensesByCategory(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Expense::whereIn('status', ['approved', 'paid'])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.name');

        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        return $query->pluck('total', 'category')->toArray();
    }

    public function getTotalExpenses(?string $startDate = null, ?string $endDate = null): float
    {
        $query = Expense::whereIn('status', ['approved', 'paid']);

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return (float) $query->sum('amount');
    }

    public function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $date = now()->format('Ymd');
        $lastExpense = Expense::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastExpense ? (int) substr($lastExpense->expense_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function generateProviderPaymentNumber(): string
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $lastPayment = ProviderPayment::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? (int) substr($lastPayment->payment_number, -4) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
