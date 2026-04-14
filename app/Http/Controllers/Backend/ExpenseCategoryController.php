<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ExpenseCategory::withCount('expenses')->get();

        return view('backend.pages.expense-categories.index', [
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Expense Categories'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expense Categories'), 'url' => null],
                ],
            ],
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.expense-categories.create', [
            'breadcrumbs' => [
                'title' => __('Create Expense Category'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expense Categories'), 'url' => route('admin.expense-categories.index')],
                    ['label' => __('Create'), 'url' => null],
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ExpenseCategory::create($validated);

        return redirect()
            ->route('admin.expense-categories.index')
            ->with('success', __('Expense category created successfully.'));
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('backend.pages.expense-categories.edit', [
            'category' => $expenseCategory,
            'breadcrumbs' => [
                'title' => __('Edit Expense Category'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expense Categories'), 'url' => route('admin.expense-categories.index')],
                    ['label' => __('Edit'), 'url' => null],
                ],
            ],
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $expenseCategory->update($validated);

        return redirect()
            ->route('admin.expense-categories.index')
            ->with('success', __('Expense category updated successfully.'));
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->is_system) {
            return redirect()
                ->back()
                ->with('error', __('System categories cannot be deleted.'));
        }

        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', __('Cannot delete category with existing expenses.'));
        }

        $expenseCategory->delete();

        return redirect()
            ->route('admin.expense-categories.index')
            ->with('success', __('Expense category deleted successfully.'));
    }
}
