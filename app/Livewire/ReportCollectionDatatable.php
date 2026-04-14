<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\ReportCollection;
use App\Services\ReportCollectionService;
use Livewire\Component;
use Livewire\WithPagination;

class ReportCollectionDatatable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $paymentStatus = '';
    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
    ];

    protected ReportCollectionService $reportCollectionService;

    public function boot(ReportCollectionService $reportCollectionService)
    {
        $this->reportCollectionService = $reportCollectionService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus()
    {
        $this->resetPage();
    }

    public function startProgress(int $id)
    {
        $reportCollection = ReportCollection::findOrFail($id);
        
        if ($this->reportCollectionService->startProgress($reportCollection)) {
            session()->flash('success', __('Request marked as In Progress.'));
        } else {
            session()->flash('error', __('Cannot change status. Invalid transition.'));
        }
    }

    public function markCompleted(int $id)
    {
        $reportCollection = ReportCollection::findOrFail($id);
        
        if ($this->reportCollectionService->markCompleted($reportCollection)) {
            session()->flash('success', __('Request marked as Completed.'));
        } else {
            session()->flash('error', __('Cannot change status. Invalid transition.'));
        }
    }

    public function getCollectionsProperty()
    {
        $query = ReportCollection::with(['assignedStaff', 'medicineOrder'])
            ->latest();

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%");
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $statistics = $this->reportCollectionService->getStatistics();

        return view('livewire.report-collection-datatable', [
            'collections' => $this->collections,
            'statistics' => $statistics,
        ]);
    }
}
