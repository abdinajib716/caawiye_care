<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\StoreHospitalRequest;
use App\Http\Requests\Hospital\UpdateHospitalRequest;
use App\Models\Hospital;
use App\Services\HospitalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function __construct(
        private HospitalService $hospitalService
    ) {
    }

    /**
     * Display a listing of hospitals.
     */
    public function index(): View
    {
        $this->authorize('hospital.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Hospitals'), 'url' => null],
        ];

        return view('backend.pages.hospitals.index', compact('breadcrumbs'));
    }

    /**
     * Show the form for creating a new hospital.
     */
    public function create(): View
    {
        $this->authorize('hospital.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Hospitals'), 'url' => route('admin.hospitals.index')],
            ['label' => __('Create'), 'url' => null],
        ];

        return view('backend.pages.hospitals.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created hospital in storage.
     */
    public function store(StoreHospitalRequest $request): RedirectResponse
    {
        $hospital = $this->hospitalService->createHospital($request->validated());

        return redirect()
            ->route('admin.hospitals.index')
            ->with('success', __('Hospital created successfully'));
    }

    /**
     * Display the specified hospital.
     */
    public function show(Hospital $hospital): View
    {
        $this->authorize('hospital.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Hospitals'), 'url' => route('admin.hospitals.index')],
            ['label' => $hospital->name, 'url' => null],
        ];

        return view('backend.pages.hospitals.show', compact('hospital', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified hospital.
     */
    public function edit(Hospital $hospital): View
    {
        $this->authorize('hospital.edit');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Hospitals'), 'url' => route('admin.hospitals.index')],
            ['label' => $hospital->name, 'url' => route('admin.hospitals.show', $hospital)],
            ['label' => __('Edit'), 'url' => null],
        ];

        return view('backend.pages.hospitals.edit', compact('hospital', 'breadcrumbs'));
    }

    /**
     * Update the specified hospital in storage.
     */
    public function update(UpdateHospitalRequest $request, Hospital $hospital): RedirectResponse
    {
        $this->hospitalService->updateHospital($hospital, $request->validated());

        return redirect()
            ->route('admin.hospitals.index')
            ->with('success', __('Hospital updated successfully'));
    }

    /**
     * Remove the specified hospital from storage.
     */
    public function destroy(Hospital $hospital): RedirectResponse
    {
        $this->authorize('hospital.delete');

        $this->hospitalService->deleteHospital($hospital);

        return redirect()
            ->route('admin.hospitals.index')
            ->with('success', __('Hospital deleted successfully'));
    }
}

