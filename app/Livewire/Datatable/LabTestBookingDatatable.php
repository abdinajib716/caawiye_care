<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\LabTestBooking;

class LabTestBookingDatatable extends Datatable
{
    public string $model = LabTestBooking::class;

    public bool $showFilters = true;
    public array $relationships = ['customer', 'assignedNurse'];

    public array $searchableColumns = ['booking_number', 'patient_name'];

    public array $filterableColumns = [
        'status' => ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'],
        'payment_status' => ['pending', 'paid', 'failed'],
    ];

    public array $sortableColumns = [
        'booking_number',
        'created_at',
        'status',
    ];

    public array $bulkActions = [];

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.lab-test-bookings.index',
            'create' => 'admin.lab-test-bookings.create',
            'show' => 'admin.lab-test-bookings.show',
            'exportPdf' => 'admin.lab-test-bookings.export-pdf',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'create' => 'lab_test_booking.create',
            'view' => 'lab_test_booking.view',
            'edit' => 'lab_test_booking.edit',
            'delete' => 'lab_test_booking.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return 'Lab Test Booking';
    }

    public function getModelNamePlural(): string
    {
        return 'Lab Test Bookings';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search bookings...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Book Lab Test');
    }

    public function getNoResultsMessage(): string
    {
        return __('No bookings found.');
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
                'id' => 'booking_number',
                'title' => __('Booking #'),
                'sortable' => true,
                'sortBy' => 'booking_number',
                'width' => 'w-1/6',
            ],
            [
                'id' => 'customer',
                'title' => __('Customer'),
                'sortable' => false,
                'width' => 'w-1/5',
                'renderContent' => 'renderCustomerColumn',
            ],
            [
                'id' => 'patient',
                'title' => __('Patient'),
                'sortable' => false,
                'width' => 'w-1/6',
            ],
            [
                'id' => 'total',
                'title' => __('Total'),
                'sortable' => false,
                'width' => 'w-1/8',
                'renderContent' => 'renderTotalColumn',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'width' => 'w-1/8',
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'provider_payment',
                'title' => __('Provider Payment'),
                'sortable' => false,
                'width' => 'w-1/8',
                'renderContent' => 'renderProviderPaymentColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'width' => 'w-1/12',
            ],
        ];
    }

    public function renderCustomerColumn($booking): string
    {
        if ($booking->customer) {
            return '<div class="text-sm">
                <div class="font-medium text-gray-900 dark:text-white">' . e($booking->customer->name) . '</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">' . e($booking->customer->phone) . '</div>
            </div>';
        }
        return '<span class="text-gray-400">-</span>';
    }

    public function renderTotalColumn($booking): string
    {
        return '<div class="text-sm font-semibold text-gray-900 dark:text-white">$' . number_format((float) $booking->total, 2) . '</div>';
    }

    public function renderStatusColumn($booking): string
    {
        $colorClass = match ($booking->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
            'in_progress' => 'bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800',
            'completed' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'cancelled' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            default => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst(str_replace('_', ' ', $booking->status)))
            . '</span>';
    }

    public function renderProviderPaymentColumn($booking): string
    {
        $status = $booking->provider_payment_status ?? 'unpaid';
        
        $colorClass = match ($status) {
            'paid' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'reversed' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            default => 'bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
        };

        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">'
            . e(ucfirst($status))
            . '</span>';
    }

    public function renderActionsColumn($booking): string
    {
        $html = '<div class="flex items-center space-x-2">';

        // View button
        $html .= '<a href="' . route('admin.lab-test-bookings.show', $booking) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800" title="' . __('View Booking') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        $html .= '</div>';

        return $html;
    }
}
