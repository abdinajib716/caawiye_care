<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('backend.pages.suppliers.index', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Suppliers'), 'url' => null],
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.suppliers.create', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Suppliers'), 'url' => route('admin.suppliers.index')],
                ['label' => __('Add Supplier'), 'url' => null],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'phone.unique' => __('A supplier with this phone number already exists.'),
            'email.unique' => __('A supplier with this email address already exists.'),
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('Supplier added successfully'));
    }

    public function show(Supplier $supplier): View
    {
        return view('backend.pages.suppliers.show', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Suppliers'), 'url' => route('admin.suppliers.index')],
                ['label' => $supplier->name, 'url' => null],
            ],
            'supplier' => $supplier,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('backend.pages.suppliers.edit', [
            'breadcrumbs' => [
                ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                ['label' => __('Suppliers'), 'url' => route('admin.suppliers.index')],
                ['label' => __('Edit Supplier'), 'url' => null],
            ],
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplier->id,
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'phone.unique' => __('A supplier with this phone number already exists.'),
            'email.unique' => __('A supplier with this email address already exists.'),
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('Supplier updated successfully'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('Supplier deleted successfully'));
    }
}
