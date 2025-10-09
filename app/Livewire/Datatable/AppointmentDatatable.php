<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\Appointment;

class AppointmentDatatable extends Datatable
{
    public string $model = Appointment::class;

    public bool $showFilters = true;
    public array $relationships = ['customer', 'hospital'];

    public array $searchableColumns = ['patient_name'];

    public array $filterableColumns = [
        'status' => ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'],
    ];

    public array $sortableColumns = [
        'appointment_time',
        'status',
        'created_at',
    ];

    public array $bulkActions = [];

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.appointments.index',
            'create' => 'admin.appointments.create',
            'show' => 'admin.appointments.show',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'appointment.create',
            'view' => 'appointment.view',
            'edit' => 'appointment.edit',
            'delete' => 'appointment.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Appointment';
    }

    public function getModelNamePlural(): string
    {
        return 'Appointments';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search appointments...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Book Appointment');
    }

    public function getNoResultsMessage(): string
    {
        return __('No appointments found.');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'id',
                'title' => __('ID'),
                'sortable' => false,
                'width' => 'w-16',
            ],
            [
                'id' => 'customer',
                'title' => __('Customer'),
                'sortable' => false,
                'width' => 'w-1/5',
                'renderContent' => 'renderCustomerColumn',
            ],
            [
                'id' => 'hospital',
                'title' => __('Hospital'),
                'sortable' => false,
                'width' => 'w-1/5',
                'renderContent' => 'renderHospitalColumn',
            ],
            [
                'id' => 'appointment_time',
                'title' => __('Appointment Time'),
                'sortable' => true,
                'sortBy' => 'appointment_time',
                'width' => 'w-1/5',
                'renderContent' => 'renderAppointmentTimeColumn',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/6',
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'width' => 'w-1/12',
            ],
        ];
    }

    public function renderCustomerColumn($appointment): string
    {
        if ($appointment->customer) {
            return '<div class="text-sm"><div class="font-medium text-gray-900">' . e($appointment->customer->name) . '</div><div class="text-xs text-gray-500">' . e($appointment->customer->phone) . '</div></div>';
        }
        return '<span class="text-gray-400">-</span>';
    }

    public function renderHospitalColumn($appointment): string
    {
        if ($appointment->hospital) {
            return '<div class="text-sm font-medium text-blue-600">' . e($appointment->hospital->name) . '</div>';
        }
        return '<span class="text-gray-400">-</span>';
    }

    public function renderAppointmentTimeColumn($appointment): string
    {
        return '<div class="text-sm text-gray-900">' . $appointment->appointment_time->format('M d, Y h:i A') . '</div>';
    }

    public function renderStatusColumn($appointment): string
    {
        $colorClass = match ($appointment->status) {
            'scheduled' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'completed' => 'bg-green-100 text-green-800 border border-green-200',
            'cancelled' => 'bg-red-100 text-red-800 border border-red-200',
            'no_show' => 'bg-gray-100 text-gray-800 border border-gray-200',
            default => 'bg-gray-100 text-gray-800 border border-gray-200',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst(str_replace('_', ' ', $appointment->status)))
            . '</span>';
    }

    public function renderActionsColumn($appointment): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.appointments.show', $appointment) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Appointment') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        $html .= '</div>';

        return $html;
    }
}

