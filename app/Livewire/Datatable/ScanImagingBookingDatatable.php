<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\ScanImagingBooking;

class ScanImagingBookingDatatable extends Datatable
{
    public string $model = ScanImagingBooking::class;
    public bool $showFilters = true;
    public array $relationships = ['customer', 'provider'];
    public array $searchableColumns = ['booking_number', 'patient_name', 'service_name'];
    public array $filterableColumns = ['status' => ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'], 'payment_status' => ['pending', 'paid', 'failed']];
    public array $sortableColumns = ['booking_number', 'appointment_time', 'created_at', 'status'];
    public array $bulkActions = [];

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.scan-imaging-bookings.index',
            'create' => 'admin.scan-imaging-bookings.create',
            'show' => 'admin.scan-imaging-bookings.show',
            'exportPdf' => 'admin.scan-imaging-bookings.export-pdf',
        ];
    }

    public function getPermissions(): array
    {
        return ['create' => 'scan_imaging_booking.create', 'view' => 'scan_imaging_booking.view', 'edit' => 'scan_imaging_booking.edit', 'delete' => 'scan_imaging_booking.delete'];
    }

    public function getModelNameSingular(): string
    {
        return 'Scan & Imaging Booking';
    }

    public function getModelNamePlural(): string
    {
        return 'Scan & Imaging Bookings';
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search bookings...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('Book Scan & Imaging');
    }

    public function getNoResultsMessage(): string
    {
        return __('No bookings found.');
    }

    protected function getHeaders(): array
    {
        return [
            ['id' => 'id', 'title' => __('ID'), 'sortable' => false, 'width' => 'w-16'],
            ['id' => 'booking_number', 'title' => __('Booking #'), 'sortable' => true, 'sortBy' => 'booking_number', 'width' => 'w-1/6'],
            ['id' => 'customer', 'title' => __('Customer'), 'sortable' => false, 'width' => 'w-1/6', 'renderContent' => 'renderCustomerColumn'],
            ['id' => 'service', 'title' => __('Service'), 'sortable' => false, 'width' => 'w-1/6', 'renderContent' => 'renderServiceColumn'],
            ['id' => 'appointment_time', 'title' => __('Appointment'), 'sortable' => true, 'sortBy' => 'appointment_time', 'width' => 'w-1/8', 'renderContent' => 'renderAppointmentColumn'],
            ['id' => 'status', 'title' => __('Status'), 'sortable' => true, 'sortBy' => 'status', 'width' => 'w-1/8', 'renderContent' => 'renderStatusColumn'],
            ['id' => 'provider_payment', 'title' => __('Provider Payment'), 'sortable' => false, 'width' => 'w-1/8', 'renderContent' => 'renderProviderPaymentColumn'],
            ['id' => 'actions', 'title' => __('Actions'), 'sortable' => false, 'width' => 'w-1/12'],
        ];
    }

    public function renderCustomerColumn($booking): string
    {
        return $booking->customer ? '<div class="text-sm"><div class="font-medium text-gray-900 dark:text-white">' . e($booking->customer->name) . '</div><div class="text-xs text-gray-500 dark:text-gray-400">' . e($booking->customer->phone) . '</div></div>' : '<span class="text-gray-400">-</span>';
    }

    public function renderServiceColumn($booking): string
    {
        return '<div class="text-sm font-medium text-gray-900 dark:text-white">' . e($booking->service_name) . '</div>';
    }

    public function renderAppointmentColumn($booking): string
    {
        return '<div class="text-sm text-gray-900 dark:text-white">' . $booking->appointment_time->format('M d, Y h:i A') . '</div>';
    }

    public function renderStatusColumn($booking): string
    {
        $colors = ['pending' => 'yellow', 'confirmed' => 'blue', 'in_progress' => 'orange', 'completed' => 'green', 'cancelled' => 'red'];
        $color = $colors[$booking->status] ?? 'gray';
        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-' . $color . '-100 text-' . $color . '-800 border border-' . $color . '-200 dark:bg-' . $color . '-900/20 dark:text-' . $color . '-400 dark:border-' . $color . '-800">' . e(ucfirst(str_replace('_', ' ', $booking->status))) . '</span>';
    }

    public function renderProviderPaymentColumn($booking): string
    {
        $status = $booking->provider_payment_status ?? 'unpaid';
        $colorClass = match ($status) {
            'paid' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'reversed' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
            default => 'bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
        };
        return '<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ' . $colorClass . '">' . e(ucfirst($status)) . '</span>';
    }

    public function renderActionsColumn($booking): string
    {
        return '<div class="flex items-center space-x-2"><a href="' . route('admin.scan-imaging-bookings.show', $booking) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800"><iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon></a></div>';
    }
}
