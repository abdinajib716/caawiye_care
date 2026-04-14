<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Customer;
use App\Models\DeliveryLocation;
use App\Models\DeliveryPrice;
use App\Models\MedicineOrder;
use App\Models\ReportCollection;
use App\Models\User;
use App\Services\ReportCollectionService;
use Livewire\Component;

class ReportCollectionBookingForm extends Component
{
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 4;

    // Customer Information
    public ?int $customerId = null;
    public array $customers = [];
    public string $customerName = '';
    public string $customerPhone = '';
    public bool $showNewCustomerForm = false;
    public ?int $medicineOrderId = null;
    public array $medicineOrders = [];

    // Patient Information
    public string $patientName = '';
    public string $patientReference = '';

    // Provider Information
    public string $providerType = '';
    public string $providerName = '';
    public string $providerAddress = '';

    // Collection Details
    public bool $deliveryRequired = false;
    public ?int $pickupLocationId = null;
    public ?int $dropoffLocationId = null;
    public string $deliveryDate = '';
    public string $deliveryTime = '';
    public string $internalNotes = '';

    // Assignment
    public ?int $assignedStaffId = null;
    public string $assignmentNotes = '';

    // Payment
    public string $paymentMethod = 'evc_plus';

    // Calculated values
    public float $serviceCharge = 0;
    public float $deliveryFee = 0;
    public float $totalAmount = 0;

    // Staff list
    public array $staffList = [];
    public array $deliveryLocations = [];
    public ?float $configuredDeliveryFee = null;
    public ?string $deliveryPriceMessage = null;

    // Provider types
    public array $providerTypes = [
        'hospital' => 'Hospital',
        'laboratory' => 'Laboratory',
        'supplier' => 'Supplier',
        'other' => 'Other',
    ];

    // Payment methods
    public array $paymentMethods = [
        'evc_plus' => 'EVC Plus',
        'e_dahab' => 'E-Dahab',
    ];

    protected ReportCollectionService $reportCollectionService;

    protected $rules = [
        'customerId' => 'required_without:showNewCustomerForm|nullable|exists:customers,id',
        'customerName' => 'required_if:showNewCustomerForm,true|nullable|string|min:2|max:255',
        'customerPhone' => 'required_if:showNewCustomerForm,true|nullable|string|min:9|max:20',
        'patientName' => 'required|string|min:2|max:255',
        'patientReference' => 'nullable|string|max:100',
        'providerType' => 'required|in:hospital,laboratory,supplier,other',
        'providerName' => 'required|string|min:2|max:255',
        'providerAddress' => 'nullable|string|max:500',
        'deliveryRequired' => 'boolean',
        'pickupLocationId' => 'required_if:deliveryRequired,true|nullable|exists:delivery_locations,id|different:dropoffLocationId',
        'dropoffLocationId' => 'required_if:deliveryRequired,true|nullable|exists:delivery_locations,id|different:pickupLocationId',
        'deliveryDate' => 'required_if:deliveryRequired,true|nullable|date|after_or_equal:today',
        'deliveryTime' => 'required_if:deliveryRequired,true|nullable',
        'internalNotes' => 'nullable|string|max:1000',
        'assignedStaffId' => 'required|exists:users,id',
        'assignmentNotes' => 'nullable|string|max:500',
        'paymentMethod' => 'required|in:evc_plus,e_dahab',
    ];

    protected $messages = [
        'customerId.required_without' => 'Please select a customer or create a new one.',
        'customerName.required_if' => 'Customer name is required for new customer.',
        'customerPhone.required_if' => 'Customer phone is required for new customer.',
        'patientName.required' => 'Patient name is required.',
        'providerType.required' => 'Provider type is required.',
        'providerName.required' => 'Provider name is required.',
        'assignedStaffId.required' => 'Assigned staff is required.',
        'deliveryDate.after_or_equal' => 'Delivery date cannot be in the past.',
    ];

    public function boot(ReportCollectionService $reportCollectionService)
    {
        $this->reportCollectionService = $reportCollectionService;
    }

    public function mount()
    {
        $this->loadCustomers();
        $this->loadMedicineOrders();
        $this->loadDeliveryLocations();
        $this->loadStaffList();
        $this->calculateTotals();
        $this->deliveryDate = now()->addDay()->format('Y-m-d');
        $this->deliveryTime = '09:00';
    }

    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get()->toArray();
    }

    public function loadMedicineOrders()
    {
        $this->medicineOrders = MedicineOrder::with(['customer', 'supplier', 'pickupLocation', 'dropoffLocation'])
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (MedicineOrder $order) => [
                'id' => $order->id,
                'label' => $order->order_number . ' - ' . ($order->customer?->name ?? __('Unknown Customer')),
                'customer_id' => $order->customer_id,
                'customer_name' => $order->customer?->name ?? '',
                'customer_phone' => $order->customer?->phone ?? '',
                'patient_name' => $order->customer?->name ?? '',
                'provider_type' => 'supplier',
                'provider_name' => $order->supplier?->name ?? '',
                'provider_address' => $order->supplier?->address
                    ?? $order->dropoffLocation?->name
                    ?? $order->pickupLocation?->name
                    ?? '',
                'delivery_required' => (bool) $order->requires_delivery,
                'pickup_location_id' => $order->pickup_location_id,
                'dropoff_location_id' => $order->dropoff_location_id,
            ])
            ->toArray();
    }

    public function loadDeliveryLocations()
    {
        $this->deliveryLocations = DeliveryLocation::orderBy('name')->get()->toArray();
    }

    public function toggleNewCustomerForm()
    {
        $this->showNewCustomerForm = !$this->showNewCustomerForm;
        if ($this->showNewCustomerForm) {
            $this->customerId = null;
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
        
        // Refresh customers list
        $this->loadCustomers();
        
        session()->flash('success', __('Customer created successfully'));
    }

    public function loadStaffList()
    {
        $this->staffList = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Superadmin', 'admin', 'agent']);
        })
            ->orderBy('first_name')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
            ])
            ->toArray();
    }

    public function updatedDeliveryRequired()
    {
        if (! $this->deliveryRequired) {
            $this->pickupLocationId = null;
            $this->dropoffLocationId = null;
            $this->configuredDeliveryFee = null;
            $this->deliveryPriceMessage = null;
        }

        $this->calculateTotals();
    }

    public function updatedPickupLocationId()
    {
        $this->calculateDeliveryFee();
        $this->calculateTotals();
    }

    public function updatedDropoffLocationId()
    {
        $this->calculateDeliveryFee();
        $this->calculateTotals();
    }

    public function updatedMedicineOrderId($value)
    {
        if (!$value) {
            return;
        }

        $order = collect($this->medicineOrders)->firstWhere('id', (int) $value);

        if (!$order) {
            return;
        }

        $this->customerId = $order['customer_id'] ?: null;
        $this->customerName = $order['customer_name'];
        $this->customerPhone = $order['customer_phone'];
        $this->patientName = $this->patientName ?: $order['patient_name'];
        $this->providerType = $order['provider_type'];
        $this->providerName = $order['provider_name'];
        $this->providerAddress = $order['provider_address'];
        $this->deliveryRequired = $order['delivery_required'];
        $this->pickupLocationId = $order['pickup_location_id'] ?: null;
        $this->dropoffLocationId = $order['dropoff_location_id'] ?: null;
        $this->showNewCustomerForm = false;
        $this->calculateDeliveryFee();
        $this->calculateTotals();
    }

    public function calculateDeliveryFee()
    {
        if (! $this->deliveryRequired || ! $this->pickupLocationId || ! $this->dropoffLocationId) {
            $this->configuredDeliveryFee = null;
            $this->deliveryPriceMessage = null;
            return;
        }

        $price = DeliveryPrice::query()
            ->where('pickup_location_id', $this->pickupLocationId)
            ->where('dropoff_location_id', $this->dropoffLocationId)
            ->first();

        if ($price) {
            $this->configuredDeliveryFee = (float) $price->price;
            $this->deliveryPriceMessage = null;
            return;
        }

        $this->configuredDeliveryFee = null;
        $this->deliveryPriceMessage = __('No delivery price configured for this route');
    }

    public function calculateTotals()
    {
        $totals = $this->reportCollectionService->calculateTotal($this->deliveryRequired, $this->configuredDeliveryFee);
        $this->serviceCharge = $totals['service_charge'];
        $this->deliveryFee = $totals['delivery_fee'];
        $this->totalAmount = $totals['total_amount'];
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->totalSteps && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep()
    {
        $rules = match ($this->currentStep) {
            1 => [
                'customerName' => $this->rules['customerName'],
                'customerPhone' => $this->rules['customerPhone'],
                'patientName' => $this->rules['patientName'],
                'patientReference' => $this->rules['patientReference'],
            ],
            2 => [
                'providerType' => $this->rules['providerType'],
                'providerName' => $this->rules['providerName'],
                'providerAddress' => $this->rules['providerAddress'],
            ],
            3 => [
                'deliveryRequired' => $this->rules['deliveryRequired'],
                'pickupLocationId' => $this->rules['pickupLocationId'],
                'dropoffLocationId' => $this->rules['dropoffLocationId'],
                'deliveryDate' => $this->rules['deliveryDate'],
                'deliveryTime' => $this->rules['deliveryTime'],
                'internalNotes' => $this->rules['internalNotes'],
                'assignedStaffId' => $this->rules['assignedStaffId'],
                'assignmentNotes' => $this->rules['assignmentNotes'],
            ],
            default => [],
        };

        $this->validate($rules);
    }

    public function submit()
    {
        $this->validate();

        try {
            // Get customer data
            $customerName = $this->customerName;
            $customerPhone = $this->customerPhone;
            
            if ($this->customerId) {
                $customer = Customer::find($this->customerId);
                if ($customer) {
                    $customerName = $customer->name;
                    $customerPhone = $customer->phone;
                }
            }

            // Process payment first
            $paymentResult = $this->reportCollectionService->processPayment([
                'customer_phone' => $customerPhone,
                'customer_name' => $customerName,
                'patient_name' => $this->patientName,
                'total_amount' => $this->totalAmount,
            ]);

            if (!$paymentResult['success']) {
                session()->flash('error', $paymentResult['message'] ?? __('Payment verification failed. Request not created.'));
                return;
            }

            if ($this->deliveryRequired && $this->pickupLocationId && $this->dropoffLocationId && $this->configuredDeliveryFee === null) {
                session()->flash('error', __('No delivery price configured for the selected route.'));
                return;
            }

            // Create the report collection request
            $reportCollection = $this->reportCollectionService->createRequest([
                'medicine_order_id' => $this->medicineOrderId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'patient_name' => $this->patientName,
                'patient_reference' => $this->patientReference ?: null,
                'provider_type' => $this->providerType,
                'provider_name' => $this->providerName,
                'provider_address' => $this->providerAddress ?: null,
                'delivery_required' => $this->deliveryRequired,
                'pickup_location_id' => $this->pickupLocationId,
                'dropoff_location_id' => $this->dropoffLocationId,
                'delivery_fee' => $this->deliveryRequired ? $this->deliveryFee : 0,
                'delivery_date' => $this->deliveryRequired ? $this->deliveryDate : null,
                'delivery_time' => $this->deliveryRequired ? $this->deliveryTime : null,
                'internal_notes' => $this->internalNotes ?: null,
                'assigned_staff_id' => $this->assignedStaffId,
                'assignment_notes' => $this->assignmentNotes ?: null,
                'payment_method' => $this->paymentMethod,
                'payment_reference' => $paymentResult['reference'],
            ]);

            session()->flash('success', __('Report collection request created successfully! Request ID: :id', ['id' => $reportCollection->request_id]));
            
            return redirect()->route('admin.collections.index');

        } catch (\Exception $e) {
            \Log::error('Report Collection creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    public function getAssignedStaffName(): string
    {
        if (!$this->assignedStaffId) {
            return 'N/A';
        }
        
        $staff = collect($this->staffList)->firstWhere('id', $this->assignedStaffId);
        return $staff['name'] ?? 'N/A';
    }

    public function render()
    {
        return view('livewire.report-collection-booking-form');
    }
}
