<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\PaymentTransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    protected PaymentTransactionService $transactionService;

    public function __construct(PaymentTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of transactions.
     */
    public function index(Request $request): View
    {
        // Get transaction statistics
        $statistics = $this->transactionService->getTransactionStatistics();

        return view('backend.pages.transactions.index', [
            'statistics' => $statistics,
            'breadcrumbs' => [
                'title' => __('Transactions'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Transactions'), 'url' => null],
                ],
            ],
        ]);
    }

    /**
     * Display the specified transaction.
     */
    public function show(PaymentTransaction $transaction): View
    {
        $transaction->load(['customer', 'order.items.service', 'order.agent']);

        return view('backend.pages.transactions.show', [
            'transaction' => $transaction,
            'breadcrumbs' => [
                'title' => __('Transaction Details'),
                'items' => [
                    ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
                    ['label' => __('Transactions'), 'url' => route('admin.transactions.index')],
                    ['label' => $transaction->reference_id, 'url' => null],
                ],
            ],
        ]);
    }
}

