<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Provider;
use App\Services\ProviderPayoutService;
use Livewire\Component;
use Livewire\WithPagination;

class ProviderPayoutManager extends Component
{
    use WithPagination;

    // Provider selection
    public array $providers = [];
    public ?int $selectedProviderId = null;
    public ?array $selectedProvider = null;

    // Date filters
    public ?string $startDate = null;
    public ?string $endDate = null;

    // Earnings filter
    public string $statusFilter = 'unpaid';

    // Provider summary
    public array $summary = [];

    // Earnings list
    public array $earnings = [];

    // Payment modal
    public bool $showPaymentModal = false;
    public string $paymentMethod = '';
    public string $transactionReference = '';
    public string $paymentNotes = '';

    protected ProviderPayoutService $payoutService;

    public function boot(ProviderPayoutService $payoutService)
    {
        $this->payoutService = $payoutService;
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
        // Load all providers for dropdown
        $this->providers = Provider::orderBy('name')->get(['id', 'name', 'phone', 'status'])->toArray();
    }

    public function updatedSelectedProviderId()
    {
        if ($this->selectedProviderId) {
            $this->selectProvider($this->selectedProviderId);
        } else {
            $this->clearProvider();
        }
    }

    public function selectProvider(int $providerId)
    {
        $this->selectedProviderId = $providerId;
        $provider = Provider::find($providerId);
        
        if ($provider) {
            $this->selectedProvider = [
                'id' => $provider->id,
                'name' => $provider->name,
                'phone' => $provider->phone,
                'status' => $provider->status,
            ];
        }

        $this->loadProviderData();
    }

    public function clearProvider()
    {
        $this->selectedProviderId = null;
        $this->selectedProvider = null;
        $this->summary = [];
        $this->earnings = [];
    }

    public function loadProviderData()
    {
        if (!$this->selectedProviderId) {
            return;
        }

        $this->summary = $this->payoutService->getProviderSummary(
            $this->selectedProviderId,
            $this->startDate,
            $this->endDate
        );

        $this->earnings = $this->payoutService->getProviderEarnings(
            $this->selectedProviderId,
            $this->startDate,
            $this->endDate,
            $this->statusFilter
        )->toArray();
    }

    public function updatedStartDate()
    {
        $this->loadProviderData();
    }

    public function updatedEndDate()
    {
        $this->loadProviderData();
    }

    public function updatedStatusFilter()
    {
        $this->loadProviderData();
    }

    public function openPaymentModal()
    {
        if (empty($this->summary) || ($this->summary['outstanding_balance'] ?? 0) <= 0) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('No Outstanding Balance'),
                'message' => __('There is no outstanding balance to pay.'),
            ]);
            return;
        }

        $this->paymentMethod = '';
        $this->transactionReference = '';
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required|in:evc,zaad,sahal,manual',
            'transactionReference' => 'nullable|string|max:255',
            'paymentNotes' => 'nullable|string|max:1000',
        ]);

        try {
            $result = $this->payoutService->payProvider(
                $this->selectedProviderId,
                $this->paymentMethod,
                $this->transactionReference ?: null,
                $this->paymentNotes ?: null
            );

            $this->closePaymentModal();
            $this->loadProviderData();

            $this->dispatch('notify', [
                'variant' => 'success',
                'title' => __('Payment Successful'),
                'message' => __('Successfully paid :amount for :count orders.', [
                    'amount' => '$' . number_format($result['total_amount'], 2),
                    'count' => $result['order_count'],
                ]),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Payment Failed'),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getUnpaidOrdersList(): array
    {
        if (!$this->selectedProviderId) {
            return [];
        }

        return $this->payoutService->getProviderEarnings(
            $this->selectedProviderId,
            null,
            null,
            'unpaid'
        )->toArray();
    }

    public function render()
    {
        return view('livewire.provider-payout-manager');
    }
}
