<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(): View
    {
        return view('backend.pages.medicines.index', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicines'), 'url' => null],
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.medicines.create', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicines'), 'url' => route('admin.medicines.index')],
                ['label' => __('Add Medicine'), 'url' => null],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:medicines,name',
        ]);

        Medicine::create($validated);

        return redirect()->route('admin.medicines.index')
            ->with('success', __('Medicine added successfully'));
    }

    public function edit(Medicine $medicine): View
    {
        return view('backend.pages.medicines.edit', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Medicines'), 'url' => route('admin.medicines.index')],
                ['label' => __('Edit Medicine'), 'url' => null],
            ],
            'medicine' => $medicine,
        ]);
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:medicines,name,' . $medicine->id,
        ]);

        $medicine->update($validated);

        return redirect()->route('admin.medicines.index')
            ->with('success', __('Medicine updated successfully'));
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $medicine->delete();

        return redirect()->route('admin.medicines.index')
            ->with('success', __('Medicine deleted successfully'));
    }
}
