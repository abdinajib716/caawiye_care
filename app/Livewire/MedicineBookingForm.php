<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Customer;
use App\Models\DeliveryLocation;
use App\Models\DeliveryPrice;
use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Models\MedicineOrderItem;
use App\Models\Supplier;
use App\Services\WaafipayService;
use Livewire\Component;

class MedicineBookingForm extends Component
{
    // Step management
    public int $currentStep = 1;

    // Step 1: Medicine & Delivery Details
    public ?int $supplierId = null;
    public array $medicines = [];
    public int $medicineCounter = 0;
    
    // Delivery
    public bool $requiresDelivery = false;
    public ?int $pickupLocationId = null;
    public ?int $dropoffLocationId = null;
    public ?float $deliveryPrice = null;
    public ?string $deliveryPriceMessage = null;

    // Step 2: Customer Information
    public string $customerSearch = '';
    public string $customerName = '';
    public string $customerPhone = '';
    public ?int $customerId = null;
    public array $matchingCustomers = [];
    public bool $showNewCustomerForm = false;

    // Data
    public array $suppliers = [];
    public array $allMedicines = [];
    public array $deliveryLocations = [];
    public ?Supplier $selectedSupplier = null;

    // Validation
    public array $validationErrors = [];

    protected WaafipayService $waafipayService;

    public function boot(WaafipayService $waafipayService)
    {
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->suppliers = Supplier::where('status', 'active')->get()->toArray();
        $this->allMedicines = Medicine::orderBy('name')->get()->toArray();
        $this->deliveryLocations = DeliveryLocation::orderBy('name')->get()->toArray();
        
        // Add first medicine row
        $this->addMedicine();
    }

    public function addMedicine()
    {
        $this->medicines[] = [
            'id' => $this->medicineCounter++,
            'medicine_id' => null,
            'medicine_name' => '',
            'quantity' => 1,
            'cost' => 0,
            'profit' => 0,
            'profit_type' => 'fixed', // 'fixed' or 'percentage'
            'is_new' => false,
        ];
    }

    public function removeMedicine($id)
    {
        $this->medicines = array_values(array_filter($this->medicines, fn($m) => $m['id'] !== $id));
    }

    public function updatedMedicines($value, $key)
    {
        // Parse the key to get index and field
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];
            
            if ($field === 'medicine_id' && isset($this->medicines[$index])) {
                $medicineId = $this->medicines[$index]['medicine_id'];
                if ($medicineId) {
                    $medicine = collect($this->allMedicines)->firstWhere('id', $medicineId);
                    if ($medicine) {
                        $this->medicines[$index]['medicine_name'] = $medicine['name'];
                        $this->medicines[$index]['is_new'] = false;
                    }
                }
            }
        }
    }

    public function updatedSupplierId()
    {
        if ($this->supplierId) {
            $this->selectedSupplier = Supplier::find($this->supplierId);
        }
    }

    public function updatedRequiresDelivery()
    {
        if (!$this->requiresDelivery) {
            $this->pickupLocationId = null;
            $this->dropoffLocationId = null;
            $this->deliveryPrice = null;
            $this->deliveryPriceMessage = null;
        }
    }

    public function updatedPickupLocationId()
    {
        $this->calculateDeliveryPrice();
    }

    public function updatedDropoffLocationId()
    {
        $this->calculateDeliveryPrice();
    }

    protected function calculateDeliveryPrice()
    {
        if ($this->pickupLocationId && $this->dropoffLocationId) {
            $price = DeliveryPrice::where('pickup_location_id', $this->pickupLocationId)
                ->where('dropoff_location_id', $this->dropoffLocationId)
                ->first();
            
            if ($price) {
                $this->deliveryPrice = (float) $price->price;
                $this->deliveryPriceMessage = null;
            } else {
                $this->deliveryPrice = null;
                $this->deliveryPriceMessage = __('No delivery price configured for this route');
            }
        } else {
            $this->deliveryPrice = null;
            $this->deliveryPriceMessage = null;
        }
    }

    public function updatedCustomerSearch()
    {
        if (strlen($this->customerSearch) >= 3) {
            $this->matchingCustomers = Customer::where('phone', 'like', '%' . $this->customerSearch . '%')
                ->orWhere('name', 'like', '%' . $this->customerSearch . '%')
                ->limit(5)
                ->get()
                ->toArray();
        } else {
            $this->matchingCustomers = [];
        }
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->customerId = $customer->id;
            $this->customerName = $customer->name;
            $this->customerPhone = $customer->phone;
            $this->matchingCustomers = [];
            $this->customerSearch = '';
            $this->showNewCustomerForm = false;
        }
    }

    public function toggleNewCustomerForm()
    {
        $this->showNewCustomerForm = !$this->showNewCustomerForm;
        if ($this->showNewCustomerForm) {
            $this->matchingCustomers = [];
            $this->customerSearch = '';
        }
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'customerName' => 'required|string|min:2',
            'customerPhone' => 'required|string|min:9',
        ]);

        $customer = Customer::create([
            'name' => $this->customerName,
            'phone' => $this->customerPhone,
        ]);

        $this->customerId = $customer->id;
        $this->showNewCustomerForm = false;
        
        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => __('Customer Created'),
            'message' => __('Customer has been created successfully'),
        ]);
    }

    public function nextStep()
    {
        $this->validationErrors = [];

        if ($this->currentStep === 1) {
            // Validate medicines
            if (empty($this->medicines)) {
                $this->validationErrors['medicines'] = __('At least one medicine is required');
                return;
            }

            foreach ($this->medicines as $index => $medicine) {
                if (empty($medicine['medicine_name'])) {
                    $this->validationErrors["medicine_{$index}_name"] = __('Medicine name is required');
                }
                if ($medicine['quantity'] <= 0) {
                    $this->validationErrors["medicine_{$index}_quantity"] = __('Quantity must be greater than 0');
                }
                if ($medicine['cost'] < 0) {
                    $this->validationErrors["medicine_{$index}_cost"] = __('Cost cannot be negative');
                }
                if ($medicine['profit'] < 0) {
                    $this->validationErrors["medicine_{$index}_profit"] = __('Profit cannot be negative');
                }
            }

            // Validate supplier
            if (!$this->supplierId) {
                $this->validationErrors['supplier'] = __('Please select a supplier');
            }

            // Validate delivery
            if ($this->requiresDelivery) {
                if (!$this->pickupLocationId) {
                    $this->validationErrors['pickup'] = __('Please select pickup location');
                }
                if (!$this->dropoffLocationId) {
                    $this->validationErrors['dropoff'] = __('Please select drop-off location');
                }
                if ($this->pickupLocationId && $this->dropoffLocationId && !$this->deliveryPrice) {
                    $this->validationErrors['delivery_price'] = __('Delivery price not configured for this route');
                }
            }

            if (empty($this->validationErrors)) {
                $this->currentStep = 2;
            }
        } elseif ($this->currentStep === 2) {
            // Validate customer selection
            if (!$this->customerId) {
                $this->validationErrors['customer'] = __('Please select a customer or create a new one');
            }

            if (empty($this->validationErrors)) {
                $this->currentStep = 3;
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function calculateTotal()
    {
        $subtotal = 0;
        foreach ($this->medicines as $medicine) {
            $cost = (float) ($medicine['cost'] ?? 0);
            $profitValue = (float) ($medicine['profit'] ?? 0);
            $profitType = $medicine['profit_type'] ?? 'fixed';
            $quantity = (int) ($medicine['quantity'] ?? 0);
            $totalCost = $cost * $quantity;
            
            // Calculate profit based on type
            if ($profitType === 'percentage') {
                $profitAmount = ($totalCost * $profitValue) / 100;
            } else {
                $profitAmount = $profitValue;
            }
            
            $subtotal += $totalCost + $profitAmount;
        }

        $delivery = $this->requiresDelivery && $this->deliveryPrice ? (float) $this->deliveryPrice : 0;
        return $subtotal + $delivery;
    }

    public function submitOrder()
    {
        if (!$this->customerId) {
            $this->validationErrors['customerId'] = 'Customer is required';
            $this->currentStep = 2;
            return;
        }

        try {
            $total = $this->calculateTotal();

            // Process payment
            $paymentResult = $this->waafipayService->processPayment([
                'phone' => $this->customerPhone,
                'amount' => $total,
                'customer_name' => $this->customerName,
                'customer_id' => $this->customerId,
                'description' => 'Medicine Order',
                'currency' => 'USD',
            ]);

            \Log::info('Payment result received', [
                'success' => $paymentResult['success'] ?? null,
                'message' => $paymentResult['message'] ?? null,
                'responseCode' => $paymentResult['response']['responseCode'] ?? null,
            ]);

            // Check if payment successful (responseCode must be 2001)
            $responseCode = $paymentResult['response']['responseCode'] ?? null;
            
            if ($responseCode !== '2001') {
                $errorMessage = $paymentResult['message'] ?? __('Payment could not be processed');
                
                if (isset($paymentResult['response']['responseMsg'])) {
                    $errorMessage = $paymentResult['response']['responseMsg'];
                }
                
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => $errorMessage,
                ]);
                
                return;
            }

            if (!isset($paymentResult['success']) || $paymentResult['success'] !== true) {
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => __('Payment verification failed'),
                ]);
                return;
            }

            \Log::info('Payment CONFIRMED successful (responseCode: 2001), creating order');

            // Calculate subtotal
            $subtotal = 0;
            foreach ($this->medicines as $medicine) {
                $cost = (float) ($medicine['cost'] ?? 0);
                $profit = (float) ($medicine['profit'] ?? 0);
                $quantity = (int) ($medicine['quantity'] ?? 0);
                $subtotal += ($cost + $profit) * $quantity;
            }

            $deliveryCost = $this->requiresDelivery && $this->deliveryPrice ? (float) $this->deliveryPrice : 0;

            // Create order
            $order = MedicineOrder::create([
                'order_number' => app(\App\Services\SequentialIdService::class)->generateMedicineOrderNumber(),
                'customer_id' => $this->customerId,
                'supplier_id' => $this->supplierId,
                'agent_id' => auth()->id(),
                'requires_delivery' => $this->requiresDelivery,
                'pickup_location_id' => $this->pickupLocationId,
                'dropoff_location_id' => $this->dropoffLocationId,
                'delivery_price' => $deliveryCost,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => 0,
                'total' => $subtotal + $deliveryCost,
                'payment_method' => 'mobile_money',
                'payment_phone' => $this->customerPhone,
                'payment_status' => 'completed',
                'payment_reference' => $paymentResult['reference_id'] ?? null,
                'status' => 'pending',
            ]);

            // Create order items and medicines
            foreach ($this->medicines as $medicine) {
                // Create medicine if new
                if (empty($medicine['medicine_id']) && !empty($medicine['medicine_name'])) {
                    $medicineModel = Medicine::firstOrCreate(['name' => $medicine['medicine_name']]);
                    $medicine['medicine_id'] = $medicineModel->id;
                }

                if ($medicine['medicine_id']) {
                    $costPerUnit = (float) ($medicine['cost'] ?? 0); // Cost per unit
                    $profitValue = (float) ($medicine['profit'] ?? 0);
                    $profitType = $medicine['profit_type'] ?? 'fixed';
                    $quantity = (int) ($medicine['quantity'] ?? 0);
                    $totalCost = $costPerUnit * $quantity; // Total cost = cost per unit × quantity
                    
                    // Calculate profit based on type
                    if ($profitType === 'percentage') {
                        $profitAmount = ($totalCost * $profitValue) / 100; // Percentage of total cost
                    } else {
                        $profitAmount = $profitValue; // Fixed amount
                    }
                    
                    $unitPrice = $costPerUnit; // Unit price = cost per unit
                    $totalPrice = $totalCost + $profitAmount; // Total = (cost × quantity) + profit

                    MedicineOrderItem::create([
                        'medicine_order_id' => $order->id,
                        'medicine_id' => $medicine['medicine_id'],
                        'medicine_name' => $medicine['medicine_name'],
                        'quantity' => $quantity,
                        'cost' => $costPerUnit,
                        'profit' => $profitValue,
                        'profit_type' => $profitType,
                        'profit_amount' => $profitAmount,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);
                }
            }

            session()->flash('success', __('Payment successful! Medicine order created. Order #:number', ['number' => $order->order_number]));
            return redirect()->route('admin.medicine-orders.show', $order);

        } catch (\Exception $e) {
            \Log::error('Medicine order error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Booking Error'),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.medicine-booking-form');
    }
}
