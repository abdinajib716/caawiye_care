<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ScanImagingService as ScanImagingServiceModel;
use App\Services\ExcelExportService;
use App\Services\ExcelImportService;
use App\Exports\ScanImagingServiceExport;
use App\Imports\ScanImagingServiceImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanImagingServiceController extends Controller
{
    /**
     * Display a listing of scan imaging services.
     */
    public function index(): View
    {
        $this->authorize('scan_imaging_service.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Services'), 'url' => null],
        ];

        $statistics = [
            'total_services' => ScanImagingServiceModel::count(),
            'active_providers' => Provider::where('status', 'active')->count(),
        ];

        return view('backend.pages.scan-imaging-services.index', compact('breadcrumbs', 'statistics'));
    }

    /**
     * Show the form for creating a new scan imaging service.
     */
    public function create(): View
    {
        $this->authorize('scan_imaging_service.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Services'), 'url' => route('admin.scan-imaging-services.index')],
            ['label' => __('Add Service'), 'url' => null],
        ];

        $providers = Provider::active()->orderBy('name')->get();

        return view('backend.pages.scan-imaging-services.create', compact('breadcrumbs', 'providers'));
    }

    /**
     * Store a newly created scan imaging service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('scan_imaging_service.create');

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:providers,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_type' => ['required', 'in:bill_provider,bill_customer'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Calculate commission and total
        $cost = $validated['cost'];
        $commissionPercentage = $validated['commission_percentage'];
        $commissionAmount = ($cost * $commissionPercentage) / 100;
        
        if ($validated['commission_type'] === 'bill_customer') {
            $total = $cost + $commissionAmount;
        } else {
            $total = $cost;
        }

        ScanImagingServiceModel::create([
            ...$validated,
            'commission_amount' => $commissionAmount,
            'total' => $total,
        ]);

        return redirect()
            ->route('admin.scan-imaging-services.index')
            ->with('success', __('Scan & imaging service created successfully'));
    }

    /**
     * Display the specified scan imaging service.
     */
    public function show(ScanImagingServiceModel $scanImagingService): View
    {
        $this->authorize('scan_imaging_service.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Services'), 'url' => route('admin.scan-imaging-services.index')],
            ['label' => $scanImagingService->service_name, 'url' => null],
        ];

        return view('backend.pages.scan-imaging-services.show', compact('scanImagingService', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified scan imaging service.
     */
    public function edit(ScanImagingServiceModel $scanImagingService): View
    {
        $this->authorize('scan_imaging_service.edit');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Scan & Imaging Services'), 'url' => route('admin.scan-imaging-services.index')],
            ['label' => __('Edit Service'), 'url' => null],
        ];

        $providers = Provider::active()->orderBy('name')->get();

        return view('backend.pages.scan-imaging-services.edit', compact('scanImagingService', 'breadcrumbs', 'providers'));
    }

    /**
     * Update the specified scan imaging service in storage.
     */
    public function update(Request $request, ScanImagingServiceModel $scanImagingService): RedirectResponse
    {
        $this->authorize('scan_imaging_service.edit');

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:providers,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_type' => ['required', 'in:bill_provider,bill_customer'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Calculate commission and total
        $cost = $validated['cost'];
        $commissionPercentage = $validated['commission_percentage'];
        $commissionAmount = ($cost * $commissionPercentage) / 100;
        
        if ($validated['commission_type'] === 'bill_customer') {
            $total = $cost + $commissionAmount;
        } else {
            $total = $cost;
        }

        $scanImagingService->update([
            ...$validated,
            'commission_amount' => $commissionAmount,
            'total' => $total,
        ]);

        return redirect()
            ->route('admin.scan-imaging-services.index')
            ->with('success', __('Scan & imaging service updated successfully'));
    }

    /**
     * Remove the specified scan imaging service from storage.
     */
    public function destroy(ScanImagingServiceModel $scanImagingService): RedirectResponse
    {
        $this->authorize('scan_imaging_service.delete');

        $scanImagingService->delete();

        return redirect()
            ->route('admin.scan-imaging-services.index')
            ->with('success', __('Scan & imaging service deleted successfully'));
    }

    public function export(ExcelExportService $exportService)
    {
        $this->authorize('viewAny', ScanImagingServiceModel::class);
        $export = new ScanImagingServiceExport();
        return $exportService->exportToCsv($export->data(), $export->headers(), $export->filename());
    }

    public function import(Request $request, ExcelImportService $importService)
    {
        $this->authorize('create', ScanImagingServiceModel::class);
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $import = new ScanImagingServiceImport();
        $missingHeaders = $importService->getMissingHeaders($request->file('file'), $import->requiredHeaders());
        if (!empty($missingHeaders)) {
            return response()->json(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'errors' => ['Missing required headers: ' . implode(', ', $missingHeaders)]]]], 422);
        }
        $result = $importService->importFromCsv($request->file('file'), $import->rules(), function ($data, $rowNumber) use ($import) {
            $import->processRow($data, $rowNumber);
        });
        return response()->json($result);
    }

    public function downloadSampleTemplate(ExcelExportService $exportService)
    {
        $this->authorize('viewAny', ScanImagingServiceModel::class);
        $export = new ScanImagingServiceExport();
        return $exportService->generateSampleTemplate($export->headers(), $export->sampleData(), 'scan_imaging_services');
    }
}
