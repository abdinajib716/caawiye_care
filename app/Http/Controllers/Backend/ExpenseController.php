<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {
    }

    public function index(Request $request): View
    {
        $statistics = $this->expenseService->getExpenseStatistics();
        $categories = ExpenseCategory::active()->get();

        return view('backend.pages.expenses.index', [
            'statistics' => $statistics,
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Expenses'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expenses'), 'url' => null],
                ],
            ],
        ]);
    }

    public function create(): View
    {
        $categories = ExpenseCategory::active()->get();

        return view('backend.pages.expenses.create', [
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Create Expense'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expenses'), 'url' => route('admin.expenses.index')],
                    ['label' => __('Create'), 'url' => null],
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'transaction_method' => 'required|in:cash,evc,edahab,bank',
            'paid_to' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        $this->expenseService->createExpense($validated);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', __('Expense created successfully.'));
    }

    public function show(Expense $expense): View
    {
        $expense->load(['category', 'createdBy', 'approvedBy', 'providerPayment']);

        return view('backend.pages.expenses.show', [
            'expense' => $expense,
            'breadcrumbs' => [
                'title' => __('Expense #:number', ['number' => $expense->expense_number]),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expenses'), 'url' => route('admin.expenses.index')],
                    ['label' => $expense->expense_number, 'url' => null],
                ],
            ],
        ]);
    }

    public function edit(Expense $expense): View
    {
        if (!$expense->canBeEdited()) {
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('error', __('This expense cannot be edited.'));
        }

        $categories = ExpenseCategory::active()->get();

        return view('backend.pages.expenses.edit', [
            'expense' => $expense,
            'categories' => $categories,
            'breadcrumbs' => [
                'title' => __('Edit Expense'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Expenses'), 'url' => route('admin.expenses.index')],
                    ['label' => $expense->expense_number, 'url' => route('admin.expenses.show', $expense)],
                    ['label' => __('Edit'), 'url' => null],
                ],
            ],
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'transaction_method' => 'required|in:cash,evc,edahab,bank',
            'paid_to' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        try {
            $this->expenseService->updateExpense($expense, $validated);
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', __('Expense updated successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        try {
            $this->expenseService->deleteExpense($expense);
            return redirect()
                ->route('admin.expenses.index')
                ->with('success', __('Expense deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function submitForApproval(Expense $expense): RedirectResponse
    {
        try {
            $this->expenseService->submitForApproval($expense);
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', __('Expense submitted for approval.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function approve(Expense $expense): RedirectResponse
    {
        try {
            $this->expenseService->approveExpense($expense);
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', __('Expense approved successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Expense $expense): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $this->expenseService->rejectExpense($expense, $request->input('rejection_reason'));
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', __('Expense rejected.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function markAsPaid(Expense $expense): RedirectResponse
    {
        try {
            $this->expenseService->markAsPaid($expense);
            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', __('Expense marked as paid.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
