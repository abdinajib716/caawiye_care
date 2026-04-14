<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LabTest;
use App\Models\Provider;
use App\Services\ExcelExportService;
use App\Services\ExcelImportService;
use App\Exports\LabTestExport;
use App\Imports\LabTestImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabTestController extends Controller
{
    /**
     * Display a listing of lab tests.
     */
    public function index(): View
    {
        $this->authorize('lab_test.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Tests'), 'url' => null],
        ];

        $statistics = [
            'total_tests' => LabTest::count(),
            'bill_provider_tests' => LabTest::where('commission_type', 'bill_provider')->count(),
            'bill_customer_tests' => LabTest::where('commission_type', 'bill_customer')->count(),
        ];

        return view('backend.pages.lab-tests.index', compact('breadcrumbs', 'statistics'));
    }

    /**
     * Show the form for creating a new lab test.
     */
    public function create(): View
    {
        $this->authorize('lab_test.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Tests'), 'url' => route('admin.lab-tests.index')],
            ['label' => __('Add Lab Test'), 'url' => null],
        ];

        $providers = Provider::active()->orderBy('name')->get();

        return view('backend.pages.lab-tests.create', compact('breadcrumbs', 'providers'));
    }

    /**
     * Store a newly created lab test in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('lab_test.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:providers,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_type' => ['required', 'in:bill_provider,bill_customer'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Calculate commission and totals
        $cost = $validated['cost'];
        $commissionPercentage = $validated['commission_percentage'];
        $commissionAmount = ($cost * $commissionPercentage) / 100;
        
        if ($validated['commission_type'] === 'bill_customer') {
            $total = $cost + $commissionAmount;
            $profit = $commissionAmount;
        } else {
            $total = $cost;
            $profit = $cost - $commissionAmount;
        }

        LabTest::create([
            ...$validated,
            'commission_amount' => $commissionAmount,
            'profit' => $profit,
            'total' => $total,
        ]);

        return redirect()
            ->route('admin.lab-tests.index')
            ->with('success', __('Lab test created successfully'));
    }

    /**
     * Display the specified lab test.
     */
    public function show(LabTest $labTest): View
    {
        $this->authorize('lab_test.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Tests'), 'url' => route('admin.lab-tests.index')],
            ['label' => $labTest->name, 'url' => null],
        ];

        return view('backend.pages.lab-tests.show', compact('labTest', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified lab test.
     */
    public function edit(LabTest $labTest): View
    {
        $this->authorize('lab_test.edit');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Lab Tests'), 'url' => route('admin.lab-tests.index')],
            ['label' => __('Edit Lab Test'), 'url' => null],
        ];

        $providers = Provider::active()->orderBy('name')->get();

        return view('backend.pages.lab-tests.edit', compact('labTest', 'breadcrumbs', 'providers'));
    }

    /**
     * Update the specified lab test in storage.
     */
    public function update(Request $request, LabTest $labTest): RedirectResponse
    {
        $this->authorize('lab_test.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider_id' => ['required', 'exists:providers,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_type' => ['required', 'in:bill_provider,bill_customer'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Calculate commission and totals
        $cost = $validated['cost'];
        $commissionPercentage = $validated['commission_percentage'];
        $commissionAmount = ($cost * $commissionPercentage) / 100;
        
        if ($validated['commission_type'] === 'bill_customer') {
            $total = $cost + $commissionAmount;
            $profit = $commissionAmount;
        } else {
            $total = $cost;
            $profit = $cost - $commissionAmount;
        }

        $labTest->update([
            ...$validated,
            'commission_amount' => $commissionAmount,
            'profit' => $profit,
            'total' => $total,
        ]);

        return redirect()
            ->route('admin.lab-tests.index')
            ->with('success', __('Lab test updated successfully'));
    }

    /**
     * Remove the specified lab test from storage.
     */
    public function destroy(LabTest $labTest): RedirectResponse
    {
        $this->authorize('lab_test.delete');

        $labTest->delete();

        return redirect()
            ->route('admin.lab-tests.index')
            ->with('success', __('Lab test deleted successfully'));
    }

    public function export(ExcelExportService $exportService)
    {
        $this->authorize('viewAny', LabTest::class);
        $export = new LabTestExport();
        return $exportService->exportToCsv($export->data(), $export->headers(), $export->filename());
    }

    public function import(Request $request, ExcelImportService $importService)
    {
        $this->authorize('create', LabTest::class);
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $import = new LabTestImport();
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
        $this->authorize('viewAny', LabTest::class);
        $export = new LabTestExport();
        return $exportService->generateSampleTemplate($export->headers(), $export->sampleData(), 'lab_tests');
    }
}
