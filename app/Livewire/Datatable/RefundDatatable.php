<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Refund;
use Livewire\Component;
use Livewire\WithPagination;

class RefundDatatable extends Datatable
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $date_from = '';
    public string $date_to = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int|string $perPage = 15;

    public array $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public array $filters = [
        'status' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
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

    public function sortBy(string $field = '')
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getRefundsProperty()
    {
        $query = Refund::with(['requestedBy', 'approvedBy']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status']);
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function getStatusBadgeClass(string $status): string
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'processing' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        ];

        return $colors[$status] ?? 'bg-gray-100 text-gray-800';
    }

    public function render(): \Illuminate\Contracts\Support\Renderable
    {
        return view('livewire.datatable.refund-datatable', [
            'refunds' => $this->refunds,
        ]);
    }
}
