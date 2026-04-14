<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ScanImagingBooking;
use App\Services\PdfExportService;
use App\Services\ScanImagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ScanImagingBookingController extends Controller
{
    public function __construct(
        private ScanImagingService $scanImagingService
    ) {
    }

    /**
     * Display a listing of scan imaging bookings.
     */
    public function index(): View
    {
        $this->authorize('scan_imaging_booking.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Bookings'), 'url' => null],
        ];

        $statistics = $this->scanImagingService->getBookingStatistics();

        return view('backend.pages.scan-imaging-bookings.index', compact('breadcrumbs', 'statistics'));
    }

    /**
     * Show the form for creating a new scan imaging booking.
     */
    public function create(): View
    {
        $this->authorize('scan_imaging_booking.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Bookings'), 'url' => route('admin.scan-imaging-bookings.index')],
            ['label' => __('Book Scan & Imaging'), 'url' => null],
        ];

        return view('backend.pages.scan-imaging-bookings.create', compact('breadcrumbs'));
    }

    /**
     * Display the specified scan imaging booking.
     */
    public function show(ScanImagingBooking $scanImagingBooking): View
    {
        $this->authorize('scan_imaging_booking.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Bookings'), 'url' => route('admin.scan-imaging-bookings.index')],
            ['label' => __('Booking #:number', ['number' => $scanImagingBooking->booking_number]), 'url' => null],
        ];

        $scanImagingBooking->load(['customer', 'scanImagingService', 'provider']);

        return view('backend.pages.scan-imaging-bookings.show', [
            'booking' => $scanImagingBooking,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function exportPdf(Request $request, PdfExportService $pdfService): BinaryFileResponse
    {
        $this->authorize('scan_imaging_booking.view');

        $query = ScanImagingBooking::with(['customer', 'scanImagingService', 'provider']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('booking_number', 'like', '%' . $search . '%')
                    ->orWhere('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('service_name', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        $pdfPath = $pdfService->generateScanImagingBookingsPdf($query->latest()->get());

        return $pdfService->downloadPdf($pdfPath, 'scan-imaging-bookings-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Confirm a scan imaging booking.
     */
    public function confirm(ScanImagingBooking $scanImagingBooking): RedirectResponse
    {
        $this->authorize('scan_imaging_booking.edit');

        $this->scanImagingService->confirmBooking($scanImagingBooking);

        return redirect()
            ->back()
            ->with('success', __('Scan & imaging booking confirmed successfully'));
    }

    /**
     * Mark scan imaging booking as in progress.
     */
    public function markInProgress(ScanImagingBooking $scanImagingBooking): RedirectResponse
    {
        $this->authorize('scan_imaging_booking.edit');

        $this->scanImagingService->markAsInProgress($scanImagingBooking);

        return redirect()
            ->back()
            ->with('success', __('Scan & imaging booking marked as in progress'));
    }

    /**
     * Complete a scan imaging booking.
     */
    public function complete(ScanImagingBooking $scanImagingBooking): RedirectResponse
    {
        $this->authorize('scan_imaging_booking.edit');

        $this->scanImagingService->completeBooking($scanImagingBooking);

        return redirect()
            ->back()
            ->with('success', __('Scan & imaging booking completed successfully'));
    }

    /**
     * Cancel a scan imaging booking.
     */
    public function cancel(Request $request, ScanImagingBooking $scanImagingBooking): RedirectResponse
    {
        $this->authorize('scan_imaging_booking.edit');

        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->scanImagingService->cancelBooking(
            $scanImagingBooking,
            $request->input('cancellation_reason')
        );

        return redirect()
            ->back()
            ->with('success', __('Scan & imaging booking cancelled successfully'));
    }
}
