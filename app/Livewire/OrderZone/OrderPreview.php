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

    // Payment method settings
    public bool $waafipayEnabled = false;
    public bool $edahabEnabled = false;

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
            $this->dispatch('show-error', ['message' => __('Please select services and customer')]);
            return;
        }

        $this->processing = true;

        try {
            // Prepare order items
            $items = [];
            foreach ($this->services as $service) {
                $items[] = [
                    'service_id' => $service['id'],
                    'service_name' => $service['name'],
                    'quantity' => $service['quantity'],
                    'unit_price' => $service['price'],
                ];
            }

            // Determine payment method and provider
            $paymentMethod = 'mobile_money';
            $paymentProvider = $this->provider;

            // Create order
            $order = $this->orderService->createOrder([
                'customer_id' => $this->customer['id'],
                'items' => $items,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'discount' => $this->discount,
                'total' => $this->total,
                'payment_method' => $paymentMethod,
                'payment_provider' => $paymentProvider,
                'payment_phone' => $this->customer['phone'],
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            // Clear the order zone
            $this->dispatch('clear-order');

            // Redirect to order details
            session()->flash('success', __('Order created successfully! Order #:number', ['number' => $order->order_number]));

            $this->redirect(route('admin.orders.show', $order), navigate: true);

        } catch (\Exception $e) {
            $this->processing = false;
            $this->dispatch('show-error', ['message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.order-zone.order-preview');
    }
}
