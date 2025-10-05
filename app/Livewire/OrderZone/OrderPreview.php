<?php

declare(strict_types=1);

namespace App\Livewire\OrderZone;

use App\Models\Service;
use App\Services\OrderService;
use App\Services\WaafipayService;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderPreview extends Component
{
    public array $services = [];
    public ?array $customer = null;
    public ?string $provider = null;
    public float $subtotal = 0;
    public float $tax = 0;
    public float $discount = 0;
    public float $total = 0;
    public bool $canProcess = false;
    public bool $processing = false;
    public int $currentStep = 1;

    // Payment method settings
    public bool $waafipayEnabled = false;
    public bool $edahabEnabled = false;

    // Payment progress UI
    public bool $showPaymentModal = false;
    public string $paymentStatusMessage = 'Initiating payment request...';
    public int $paymentStep = 0; // 0=not started, 1=request sent, 2=waiting confirmation, 3=creating order

    protected OrderService $orderService;
    protected WaafipayService $waafipayService;

    public function boot(OrderService $orderService, WaafipayService $waafipayService)
    {
        $this->orderService = $orderService;
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->loadPaymentSettings();
        $this->calculateTotals();
    }

    public function loadPaymentSettings()
    {
        $this->waafipayEnabled = (bool) config('settings.waafipay_enabled', false);
        $this->edahabEnabled = (bool) config('settings.edahab_enabled', false);
    }

    #[On('services-updated')]
    public function updateServices($data)
    {
        $this->services = $data['services'] ?? [];
        $this->calculateTotals();
        $this->checkCanProcess();
    }

    #[On('service-details-completed')]
    public function updateServicesWithFieldData($data)
    {
        $this->services = $data['services'] ?? [];
        $this->calculateTotals();
        $this->checkCanProcess();
    }

    #[On('customer-updated')]
    public function updateCustomer($data)
    {
        $this->customer = $data['customer'] ?? null;
        $this->checkCanProcess();
    }

    #[On('provider-detected')]
    public function updateProvider($data)
    {
        $this->provider = $data['provider'] ?? null;
    }

    #[On('step-changed')]
    public function updateCurrentStep($step)
    {
        $this->currentStep = $step;
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;

        foreach ($this->services as $service) {
            $this->subtotal += $service['total'] ?? 0;
        }

        // Calculate tax (0% for now, can be configured)
        $this->tax = 0;

        // Calculate discount (0 for now, can be added later)
        $this->discount = 0;

        // Calculate total
        $this->total = $this->subtotal + $this->tax - $this->discount;
    }

    public function checkCanProcess()
    {
        $this->canProcess = !empty($this->services) && !empty($this->customer);
    }

    public function processOrder()
    {
        if (!$this->canProcess) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Cannot Process Order'),
                'message' => __('Please select services and customer'),
            ]);
            return;
        }

        // Show payment modal and reset progress
        $this->showPaymentModal = true;
        $this->processing = true;
        $this->paymentStep = 0;
        $this->paymentStatusMessage = __('Initiating payment request...');

        try {
            // Prepare order items
            $items = [];
            foreach ($this->services as $service) {
                $item = [
                    'service_id' => $service['id'],
                    'service_name' => $service['name'],
                    'quantity' => $service['quantity'],
                    'unit_price' => $service['price'],
                ];

                // Add field data if present
                if (!empty($service['field_data'])) {
                    $item['field_data'] = $service['field_data'];
                }

                $items[] = $item;
            }

            // Determine payment method and provider
            $paymentMethod = 'mobile_money';
            $paymentProvider = $this->provider;

            // Update progress: Sending payment request
            $this->paymentStep = 1;
            $this->paymentStatusMessage = __('Sending payment request to WaafiPay...');

            // Process payment through WaafiPay FIRST
            $paymentResult = $this->waafipayService->processPayment([
                'phone' => $this->customer['phone'],
                'amount' => $this->total,
                'customer_name' => $this->customer['name'],
                'customer_id' => $this->customer['id'],
                'description' => 'Order payment for ' . count($items) . ' service(s)',
                'currency' => 'USD',
            ]);

            // Check if payment was successful
            if (!$paymentResult['success']) {
                $this->processing = false;
                $this->showPaymentModal = false;
                $this->paymentStep = 0;
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => $paymentResult['message'] ?? __('Payment could not be processed'),
                ]);
                return;
            }

            // Update progress: Waiting for confirmation
            $this->paymentStep = 2;
            $this->paymentStatusMessage = __('Payment request sent. Waiting for confirmation from provider...');

            // If payment is pending, dispatch event to start polling
            if (isset($paymentResult['pending']) && $paymentResult['pending']) {
                // Check if reference_id exists
                if (!isset($paymentResult['reference_id'])) {
                    $this->processing = false;
                    $this->showPaymentModal = false;
                    $this->paymentStep = 0;
                    $this->dispatch('notify', [
                        'variant' => 'error',
                        'title' => __('Payment Failed'),
                        'message' => $paymentResult['message'] ?? __('Payment reference not received from provider'),
                    ]);
                    return;
                }

                // Keep modal open and show waiting state
                $this->paymentStatusMessage = __('Waiting for payment confirmation. Please check your phone...');

                $this->dispatch('payment-pending', [
                    'reference_id' => $paymentResult['reference_id'],
                    'order_data' => [
                        'customer_id' => $this->customer['id'],
                        'items' => $items,
                        'tax' => $this->tax,
                        'discount' => $this->discount,
                        'payment_method' => $paymentMethod,
                        'payment_provider' => $paymentProvider,
                        'payment_phone' => $this->customer['phone'],
                    ],
                ]);
                return;
            }

            // Payment completed immediately - create order
            if (isset($paymentResult['transaction'])) {
                // Update progress: Creating order
                $this->paymentStep = 3;
                $this->paymentStatusMessage = __('Payment confirmed! Creating your order...');

                $order = $this->orderService->createOrderFromTransaction(
                    $paymentResult['transaction'],
                    [
                        'customer_id' => $this->customer['id'],
                        'items' => $items,
                        'tax' => $this->tax,
                        'discount' => $this->discount,
                        'payment_method' => $paymentMethod,
                        'payment_provider' => $paymentProvider,
                        'payment_phone' => $this->customer['phone'],
                    ]
                );

                // Clear the order zone
                $this->dispatch('clear-order');

                // Close modal and redirect
                $this->showPaymentModal = false;
                $this->processing = false;

                // Redirect to order details
                session()->flash('success', __('Payment successful! Order #:number created', ['number' => $order->order_number]));
                $this->redirect(route('admin.orders.show', $order), navigate: true);
            }

        } catch (\Exception $e) {
            $this->processing = false;
            $this->showPaymentModal = false;
            $this->paymentStep = 0;
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Error'),
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cancel the order and reset the form.
     */
    public function cancelOrder()
    {
        // Close payment modal if open
        $this->showPaymentModal = false;
        $this->processing = false;
        $this->paymentStep = 0;
        $this->paymentStatusMessage = 'Initiating payment request...';

        // Clear the order zone
        $this->dispatch('clear-order');

        $this->dispatch('notify', [
            'variant' => 'info',
            'title' => __('Order Cancelled'),
            'message' => __('The order has been cancelled'),
        ]);
    }



    /**
     * Check payment status (called by frontend polling).
     */
    public function checkPaymentStatus(string $referenceId)
    {
        $result = $this->waafipayService->checkPaymentStatus($referenceId);

        if (!$result['success']) {
            return [
                'status' => 'error',
                'message' => $result['message'],
            ];
        }

        $transaction = $result['transaction'];

        return [
            'status' => $transaction->status,
            'transaction_id' => $transaction->id,
            'message' => $result['message'] ?? '',
        ];
    }

    /**
     * Create order after payment confirmation.
     */
    public function createOrderAfterPayment(int $transactionId, array $orderData)
    {
        try {
            $transaction = \App\Models\PaymentTransaction::find($transactionId);

            if (!$transaction || !$transaction->isCompleted()) {
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Not Completed'),
                    'message' => __('Payment must be completed before creating order'),
                ]);
                return;
            }

            // Create order from completed transaction
            $order = $this->orderService->createOrderFromTransaction($transaction, $orderData);

            // Clear the order zone
            $this->dispatch('clear-order');

            // Redirect to order details
            session()->flash('success', __('Payment successful! Order #:number created', ['number' => $order->order_number]));
            $this->redirect(route('admin.orders.show', $order), navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Error Creating Order'),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.order-zone.order-preview');
    }
}
