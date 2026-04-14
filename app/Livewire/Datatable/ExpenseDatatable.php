<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseDatatable extends Datatable
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $category_id = '';
    public string $transaction_method = '';
    public string $date_from = '';
    public string $date_to = '';
    public string $sortField = 'expense_date';
    public string $sortDirection = 'desc';
    public int|string $perPage = 15;

    public array $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category_id' => ['except' => ''],
        'sortField' => ['except' => 'expense_date'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public array $filters = [
        'status' => [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
        ],
        'transaction_method' => [
            'cash' => 'Cash',
            'evc' => 'EVC Plus',
            'edahab' => 'E-Dahab',
            'bank' => 'Bank Transfer',
        ],
    ];

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function sortBy(string $field = '')
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getExpensesProperty()
    {
        $query = Expense::with(['category', 'createdBy', 'approvedBy']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->transaction_method) {
            $query->where('transaction_method', $this->transaction_method);
        }

        if ($this->date_from) {
            $query->whereDate('expense_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('expense_date', '<=', $this->date_to);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return ExpenseCategory::active()->orderBy('name')->get();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'category_id', 'transaction_method']);
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function getStatusBadgeClass(string $status): string
    {
        $colors = [
            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'pending_approval' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        ];

        return $colors[$status] ?? 'bg-gray-100 text-gray-800';
    }

    public function render(): \Illuminate\Contracts\Support\Renderable
    {
        return view('livewire.datatable.expense-datatable', [
            'expenses' => $this->expenses,
            'categories' => $this->categories,
        ]);
    }
}
