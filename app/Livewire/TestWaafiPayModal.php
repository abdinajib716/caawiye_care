<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\WaafipayService;
use Livewire\Attributes\On;
use Livewire\Component;

class TestWaafiPayModal extends Component
{
    public bool $showModal = false;
    public string $phone = '';
    public float $amount = 1.00;
    public bool $processing = false;
    public string $processingMessage = '';

    protected WaafipayService $waafipayService;

    public function boot(WaafipayService $waafipayService): void
    {
        $this->waafipayService = $waafipayService;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:9|max:12',
            'amount' => 'required|numeric|min:0.01|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('Phone number is required'),
            'phone.min' => __('Phone number must be at least 9 digits'),
            'phone.max' => __('Phone number must not exceed 12 digits'),
            'amount.required' => __('Amount is required'),
            'amount.min' => __('Amount must be at least $0.01'),
            'amount.max' => __('Test amount cannot exceed $1000'),
        ];
    }

    #[On('openTestWaafiPayModal')]
    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset(['phone', 'processing', 'processingMessage']);
        $this->amount = 1.00;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['phone', 'amount', 'processing', 'processingMessage']);
    }

    public function updatedPhone(): void
    {
        // Real-time phone validation
        $this->validateOnly('phone');
    }

    public function sendPayment(): void
    {
        $this->validate();

        $this->processing = true;
        $this->processingMessage = __('Processing payment...');

        try {
            // Check if WaafiPay is enabled
            if (!$this->waafipayService->isEnabled()) {
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('WaafiPay Disabled'),
                    'message' => __('WaafiPay is not enabled. Please enable it in settings.'),
                ]);
                $this->processing = false;
                return;
            }

            // Check if credentials are configured
            if (!$this->waafipayService->validateCredentials()) {
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Configuration Error'),
                    'message' => __('WaafiPay credentials are not configured properly.'),
                ]);
                $this->processing = false;
                return;
            }

            // Process payment
            $result = $this->waafipayService->processPayment([
                'phone' => $this->phone,
                'amount' => $this->amount,
                'customer_name' => 'Test Payment',
                'description' => 'WaafiPay Test Payment',
                'currency' => 'USD',
            ]);

            if ($result['success']) {
                if (isset($result['pending']) && $result['pending']) {
                    $this->dispatch('notify', [
                        'variant' => 'info',
                        'title' => __('Payment Pending'),
                        'message' => $result['message'],
                    ]);
                } else {
                    $this->dispatch('notify', [
                        'variant' => 'success',
                        'title' => __('Payment Successful'),
                        'message' => $result['message'],
                    ]);
                }
                $this->closeModal();
            } else {
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => $result['message'],
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Error'),
                'message' => $e->getMessage(),
            ]);
        } finally {
            $this->processing = false;
            $this->processingMessage = '';
        }
    }

    public function render()
    {
        return view('livewire.test-waafi-pay-modal');
    }
}
