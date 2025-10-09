<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {
    }

    /**
     * Display a listing of appointments.
     */
    public function index(): View
    {
        $this->authorize('appointment.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Appointments'), 'url' => null],
        ];

        return view('backend.pages.appointments.index', compact('breadcrumbs'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(): View
    {
        $this->authorize('appointment.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Appointments'), 'url' => route('admin.appointments.index')],
            ['label' => __('Book Appointment'), 'url' => null],
        ];

        return view('backend.pages.appointments.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('appointment.create');

        // Validation and creation logic will be handled by Livewire component
        // This is a placeholder for direct form submission if needed

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', __('Appointment booked successfully'));
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): View
    {
        $this->authorize('appointment.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Appointments'), 'url' => route('admin.appointments.index')],
            ['label' => __('Appointment #:id', ['id' => $appointment->id]), 'url' => null],
        ];

        return view('backend.pages.appointments.show', compact('appointment', 'breadcrumbs'));
    }

    /**
     * Reschedule an appointment.
     */
    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('appointment.edit');

        $request->validate([
            'appointment_time' => ['required', 'date', 'after:now'],
        ]);

        $this->appointmentService->rescheduleAppointment(
            $appointment,
            $request->input('appointment_time')
        );

        return redirect()
            ->back()
            ->with('success', __('Appointment rescheduled successfully'));
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('appointment.edit');

        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->appointmentService->cancelAppointment(
            $appointment,
            $request->input('cancellation_reason')
        );

        return redirect()
            ->back()
            ->with('success', __('Appointment cancelled successfully'));
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(Appointment $appointment): RedirectResponse
    {
        $this->authorize('appointment.edit');

        $this->appointmentService->confirmAppointment($appointment);

        return redirect()
            ->back()
            ->with('success', __('Appointment confirmed successfully'));
    }

    /**
     * Complete an appointment.
     */
    public function complete(Appointment $appointment): RedirectResponse
    {
        $this->authorize('appointment.edit');

        $this->appointmentService->completeAppointment($appointment);

        return redirect()
            ->back()
            ->with('success', __('Appointment completed successfully'));
    }
}

