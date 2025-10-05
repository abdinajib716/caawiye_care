<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Services\DoctorService;
use App\Services\HospitalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        private DoctorService $doctorService,
        private HospitalService $hospitalService
    ) {
    }

    /**
     * Display a listing of doctors.
     */
    public function index(): View
    {
        $this->authorize('doctor.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Doctors'), 'url' => null],
        ];

        return view('backend.pages.doctors.index', compact('breadcrumbs'));
    }

    /**
     * Show the form for creating a new doctor.
     */
    public function create(): View
    {
        $this->authorize('doctor.create');

        $hospitals = $this->hospitalService->getActiveHospitals();

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Doctors'), 'url' => route('admin.doctors.index')],
            ['label' => __('Create'), 'url' => null],
        ];

        return view('backend.pages.doctors.create', compact('breadcrumbs', 'hospitals'));
    }

    /**
     * Store a newly created doctor in storage.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $doctor = $this->doctorService->createDoctor($request->validated());

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', __('Doctor created successfully'));
    }

    /**
     * Display the specified doctor.
     */
    public function show(Doctor $doctor): View
    {
        $this->authorize('doctor.view');

        $doctor->load('hospital');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Doctors'), 'url' => route('admin.doctors.index')],
            ['label' => $doctor->name, 'url' => null],
        ];

        return view('backend.pages.doctors.show', compact('doctor', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified doctor.
     */
    public function edit(Doctor $doctor): View
    {
        $this->authorize('doctor.edit');

        $hospitals = $this->hospitalService->getActiveHospitals();
        $doctor->load('hospital');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Doctors'), 'url' => route('admin.doctors.index')],
            ['label' => $doctor->name, 'url' => route('admin.doctors.show', $doctor)],
            ['label' => __('Edit'), 'url' => null],
        ];

        return view('backend.pages.doctors.edit', compact('doctor', 'hospitals', 'breadcrumbs'));
    }

    /**
     * Update the specified doctor in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->doctorService->updateDoctor($doctor, $request->validated());

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', __('Doctor updated successfully'));
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->authorize('doctor.delete');

        $this->doctorService->deleteDoctor($doctor);

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', __('Doctor deleted successfully'));
    }

    /**
     * Get doctors by hospital for cascading dropdown.
     */
    public function getDoctorsByHospital(Hospital $hospital): JsonResponse
    {
        $this->authorize('doctor.view');

        $doctors = $this->doctorService->getActiveDoctorsByHospital($hospital->id);

        return response()->json([
            'success' => true,
            'data' => $doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                ];
            }),
        ]);
    }
}

