<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LabTestBooking;
use App\Services\LabTestBookingService;
use App\Services\PdfExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LabTestBookingController extends Controller
{
    public function __construct(
        private LabTestBookingService $labTestBookingService
    ) {
    }

    /**
     * Display a listing of lab test bookings.
     */
    public function index(): View
    {
        $this->authorize('lab_test_booking.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Test Bookings'), 'url' => null],
        ];

        $statistics = $this->labTestBookingService->getBookingStatistics();

        return view('backend.pages.lab-test-bookings.index', compact('breadcrumbs', 'statistics'));
    }

    /**
     * Show the form for creating a new lab test booking.
     */
    public function create(): View
    {
        $this->authorize('lab_test_booking.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Test Bookings'), 'url' => route('admin.lab-test-bookings.index')],
            ['label' => __('Book Lab Test'), 'url' => null],
        ];

        return view('backend.pages.lab-test-bookings.create', compact('breadcrumbs'));
    }

    /**
     * Display the specified lab test booking.
     */
    public function show(LabTestBooking $labTestBooking): View
    {
        $this->authorize('lab_test_booking.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Test Bookings'), 'url' => route('admin.lab-test-bookings.index')],
            ['label' => __('Booking #:number', ['number' => $labTestBooking->booking_number]), 'url' => null],
        ];

        $labTestBooking->load(['customer', 'assignedNurse', 'items.labTest.provider']);
        
        $booking = $labTestBooking;

        return view('backend.pages.lab-test-bookings.show', compact('booking', 'breadcrumbs'));
    }

    public function exportPdf(Request $request, PdfExportService $pdfService): BinaryFileResponse
    {
        $this->authorize('lab_test_booking.view');

        $query = LabTestBooking::with(['customer', 'assignedNurse', 'items.labTest.provider']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('booking_number', 'like', '%' . $search . '%')
                    ->orWhere('patient_name', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        $pdfPath = $pdfService->generateLabTestBookingsPdf($query->latest()->get());

        return $pdfService->downloadPdf($pdfPath, 'lab-test-bookings-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Confirm a lab test booking.
     */
    public function confirm(LabTestBooking $labTestBooking): RedirectResponse
    {
        $this->authorize('lab_test_booking.edit');

        $this->labTestBookingService->confirmBooking($labTestBooking);

        return redirect()
            ->back()
            ->with('success', __('Lab test booking confirmed successfully'));
    }

    /**
     * Mark lab test booking as in progress.
     */
    public function markInProgress(LabTestBooking $labTestBooking): RedirectResponse
    {
        $this->authorize('lab_test_booking.edit');

        $this->labTestBookingService->markAsInProgress($labTestBooking);

        return redirect()
            ->back()
            ->with('success', __('Lab test booking marked as in progress'));
    }

    /**
     * Complete a lab test booking.
     */
    public function complete(LabTestBooking $labTestBooking): RedirectResponse
    {
        $this->authorize('lab_test_booking.edit');

        $this->labTestBookingService->completeBooking($labTestBooking);

        return redirect()
            ->back()
            ->with('success', __('Lab test booking completed successfully'));
    }

    /**
     * Cancel a lab test booking.
     */
    public function cancel(Request $request, LabTestBooking $labTestBooking): RedirectResponse
    {
        $this->authorize('lab_test_booking.edit');

        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->labTestBookingService->cancelBooking(
            $labTestBooking,
            $request->input('cancellation_reason')
        );

        return redirect()
            ->back()
            ->with('success', __('Lab test booking cancelled successfully'));
    }
}
