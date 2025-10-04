<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Services\AppointmentService;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentDatatable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'appointment_time';
    public string $sortDirection = 'asc';
    public int $perPage = 15;

    protected AppointmentService $appointmentService;

    public function boot(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
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
        $appointments = $this->appointmentService->getPaginatedAppointments([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'sort' => $this->sortField,
            'direction' => $this->sortDirection,
        ], $this->perPage);

        return view('livewire.datatable.appointment-datatable', [
            'appointments' => $appointments,
        ]);
    }
}

