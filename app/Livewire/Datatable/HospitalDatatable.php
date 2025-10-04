<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Hospital;
use App\Services\HospitalService;
use Livewire\Component;
use Livewire\WithPagination;

class HospitalDatatable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;

    protected HospitalService $hospitalService;

    public function boot(HospitalService $hospitalService)
    {
        $this->hospitalService = $hospitalService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $hospitals = $this->hospitalService->getPaginatedHospitals([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'sort' => $this->sortField,
            'direction' => $this->sortDirection,
        ], $this->perPage);

        return view('livewire.datatable.hospital-datatable', [
            'hospitals' => $hospitals,
        ]);
    }
}

