<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\ProviderService;
use App\Services\ExcelExportService;
use App\Services\ExcelImportService;
use App\Exports\ProviderExport;
use App\Imports\ProviderImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function __construct(
        private ProviderService $providerService
    ) {
    }

    /**
     * Display a listing of providers.
     */
    public function index(): View
    {
        $this->authorize('provider.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Providers'), 'url' => null],
        ];

        $statistics = [
            'total_providers' => Provider::count(),
            'active_providers' => Provider::where('status', 'active')->count(),
            'inactive_providers' => Provider::where('status', 'inactive')->count(),
        ];

        return view('backend.pages.providers.index', compact('breadcrumbs', 'statistics'));
    }

    /**
     * Show the form for creating a new provider.
     */
    public function create(): View
    {
        $this->authorize('provider.create');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Providers'), 'url' => route('admin.providers.index')],
            ['label' => __('Add Provider'), 'url' => null],
        ];

        return view('backend.pages.providers.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created provider in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('provider.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $this->providerService->createProvider($validated);

        return redirect()
            ->route('admin.providers.index')
            ->with('success', __('Provider created successfully'));
    }

    /**
     * Display the specified provider.
     */
    public function show(Provider $provider): View
    {
        $this->authorize('provider.view');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Providers'), 'url' => route('admin.providers.index')],
            ['label' => $provider->name, 'url' => null],
        ];

        return view('backend.pages.providers.show', compact('provider', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified provider.
     */
    public function edit(Provider $provider): View
    {
        $this->authorize('provider.edit');

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Providers'), 'url' => route('admin.providers.index')],
            ['label' => __('Edit Provider'), 'url' => null],
        ];

        return view('backend.pages.providers.edit', compact('provider', 'breadcrumbs'));
    }

    /**
     * Update the specified provider in storage.
     */
    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $this->authorize('provider.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $this->providerService->updateProvider($provider, $validated);

        return redirect()
            ->route('admin.providers.index')
            ->with('success', __('Provider updated successfully'));
    }

    /**
     * Remove the specified provider from storage.
     */
    public function destroy(Provider $provider): RedirectResponse
    {
        $this->authorize('provider.delete');

        $this->providerService->deleteProvider($provider);

        return redirect()
            ->route('admin.providers.index')
            ->with('success', __('Provider deleted successfully'));
    }

    public function export(ExcelExportService $exportService)
    {
        $this->authorize('viewAny', Provider::class);
        $export = new ProviderExport();
        return $exportService->exportToCsv($export->data(), $export->headers(), $export->filename());
    }

    public function import(Request $request, ExcelImportService $importService)
    {
        $this->authorize('create', Provider::class);
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $import = new ProviderImport();
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
        $this->authorize('viewAny', Provider::class);
        $export = new ProviderExport();
        return $exportService->generateSampleTemplate($export->headers(), $export->sampleData(), 'providers');
    }
}
