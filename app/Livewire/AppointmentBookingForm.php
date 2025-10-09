<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Services\OrderService;
use App\Services\WaafipayService;
use Livewire\Component;

class AppointmentBookingForm extends Component
{
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 3;

    // Appointment details
    public string $appointmentType = 'self';
    public string $patientName = '';
    public ?int $hospitalId = null;
    public ?int $doctorId = null;
    public string $appointmentDateTime = '';

    // Customer details
    public string $customerSearch = '';
    public string $customerName = '';
    public string $customerPhone = '';
    public ?int $customerId = null;
    public bool $showNewCustomerForm = false;
    public array $matchingCustomers = [];

    // Data
    public array $hospitals = [];
    public array $doctors = [];
    public ?Doctor $selectedDoctor = null;

    // Validation errors
    public array $validationErrors = [];

    protected OrderService $orderService;
    protected WaafipayService $waafipayService;

    public function boot(OrderService $orderService, WaafipayService $waafipayService)
    {
        $this->orderService = $orderService;
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->hospitals = Hospital::active()->orderBy('name')->get()->toArray();
        $this->appointmentDateTime = now()->addDay()->setTime(9, 0)->format('Y-m-d H:i');
    }

    public function updatedHospitalId()
    {
        $this->doctorId = null;
        $this->selectedDoctor = null;
        $this->loadDoctors();
    }

    public function updatedDoctorId()
    {
        if ($this->doctorId) {
            $this->selectedDoctor = Doctor::with('hospital')->find($this->doctorId);
        } else {
            $this->selectedDoctor = null;
        }
    }

    public function updatedCustomerSearch()
    {
        $this->searchCustomers();
    }

    public function loadDoctors()
    {
        if ($this->hospitalId) {
            $this->doctors = Doctor::active()
                ->where('hospital_id', $this->hospitalId)
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            $this->doctors = [];
        }
    }

    public function searchCustomers()
    {
        if (empty($this->customerSearch)) {
            $this->matchingCustomers = [];
            return;
        }

        $this->matchingCustomers = Customer::where(function ($query) {
            $query->where('phone', 'like', "%{$this->customerSearch}%")
                  ->orWhere('name', 'like', "%{$this->customerSearch}%");
        })
        ->limit(10)
        ->get()
        ->toArray();
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);

        if ($customer) {
            $this->customerId = $customer->id;
            $this->customerName = $customer->name;
            $this->customerPhone = $customer->phone;
            $this->customerSearch = $customer->name . ' (' . $customer->phone . ')';
            $this->matchingCustomers = [];
            $this->showNewCustomerForm = false;
        }
    }

    public function toggleNewCustomerForm()
    {
        $this->showNewCustomerForm = !$this->showNewCustomerForm;

        if ($this->showNewCustomerForm) {
            $this->matchingCustomers = [];
            if (preg_match('/^[0-9+]+$/', $this->customerSearch)) {
                $this->customerPhone = $this->customerSearch;
            }
        } else {
            $this->reset(['customerName', 'customerPhone']);
        }
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'customerPhone' => 'required|string|min:9',
            'customerName' => 'required|string|max:255',
        ]);

        $customer = Customer::create([
            'phone' => $this->customerPhone,
            'name' => $this->customerName,
            'country_code' => '252',
        ]);

        $this->customerId = $customer->id;
        $this->customerSearch = $customer->name . ' (' . $customer->phone . ')';
        $this->showNewCustomerForm = false;

        session()->flash('message', 'Customer created successfully!');
    }

    public function nextStep()
    {
        $this->validationErrors = [];

        if ($this->currentStep === 1) {
            // Validate appointment details
            if (!$this->hospitalId) {
                $this->validationErrors['hospitalId'] = 'Please select a hospital';
            }
            if (!$this->doctorId) {
                $this->validationErrors['doctorId'] = 'Please select a doctor';
            }
            if (empty($this->appointmentDateTime)) {
                $this->validationErrors['appointmentDateTime'] = 'Please select appointment date and time';
            }
            if ($this->appointmentType === 'someone_else' && empty($this->patientName)) {
                $this->validationErrors['patientName'] = 'Please enter patient name';
            }

            // Validate future date
            if (!empty($this->appointmentDateTime) && strtotime($this->appointmentDateTime) <= time()) {
                $this->validationErrors['appointmentDateTime'] = 'Appointment must be in the future';
            }
        } elseif ($this->currentStep === 2) {
            // Validate customer details
            if (!$this->customerId && empty($this->customerName)) {
                $this->validationErrors['customerName'] = 'Please select or create a customer';
            }
        }

        if (empty($this->validationErrors)) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submitAppointment()
    {
        // Validate customer
        if (!$this->customerId) {
            $this->validationErrors['customerId'] = 'Customer is required';
            $this->currentStep = 2;
            return;
        }

        try {
            // Process payment through WaafiPay
            $paymentResult = $this->waafipayService->processPayment([
                'phone' => $this->customerPhone,
                'amount' => $this->selectedDoctor->total ?? 0,
                'customer_name' => $this->customerName,
                'customer_id' => $this->customerId,
                'description' => 'Appointment - ' . ($this->selectedDoctor->name ?? 'Doctor'),
                'currency' => 'USD',
            ]);

            // Log payment result for debugging
            \Log::info('Payment result received', [
                'success' => $paymentResult['success'] ?? null,
                'message' => $paymentResult['message'] ?? null,
                'response' => $paymentResult['response'] ?? null,
                'responseCode' => $paymentResult['response']['responseCode'] ?? null,
            ]);

            // CRITICAL: Only proceed if responseCode is EXACTLY 2001 (success)
            $responseCode = $paymentResult['response']['responseCode'] ?? null;
            
            if ($responseCode !== '2001') {
                // Get the exact error message from WaafiPay API
                $errorMessage = $paymentResult['message'] ?? __('Payment could not be processed');
                
                // If there's a response with more details, use it
                if (isset($paymentResult['response']['responseMsg'])) {
                    $errorMessage = $paymentResult['response']['responseMsg'];
                } elseif (isset($paymentResult['response']['errorMessage'])) {
                    $errorMessage = $paymentResult['response']['errorMessage'];
                }
                
                \Log::warning('Payment NOT successful - responseCode is not 2001', [
                    'responseCode' => $responseCode,
                    'error_message' => $errorMessage,
                    'payment_result' => $paymentResult,
                ]);
                
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => $errorMessage,
                ]);
                
                return; // STOP HERE - Do not create order
            }

            // Verify success flag is also true
            if (!isset($paymentResult['success']) || $paymentResult['success'] !== true) {
                \Log::error('Payment responseCode is 2001 but success flag is not true', [
                    'success' => $paymentResult['success'] ?? null,
                    'result' => $paymentResult
                ]);
                
                $this->dispatch('notify', [
                    'variant' => 'error',
                    'title' => __('Payment Failed'),
                    'message' => __('Payment verification failed'),
                ]);
                return;
            }

            \Log::info('Payment CONFIRMED successful (responseCode: 2001), creating order and appointment');

            // Payment successful - Create order and appointment
            $appointmentCost = (float) ($this->selectedDoctor->total ?? 0);
            
            $order = Order::create([
                'order_number' => 'APT-' . time(),
                'customer_id' => $this->customerId,
                'agent_id' => auth()->id(),
                'subtotal' => $appointmentCost,
                'tax' => 0,
                'discount' => 0,
                'total' => $appointmentCost,
                'payment_method' => 'mobile_money',
                'payment_phone' => $this->customerPhone,
                'payment_status' => 'completed',
                'payment_reference' => $paymentResult['reference_id'] ?? null,
                'status' => 'completed',
            ]);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'service_id' => 1,
                'service_name' => 'Appointment - ' . ($this->selectedDoctor->name ?? 'Doctor'),
                'quantity' => 1,
                'unit_price' => $appointmentCost,
                'price' => $appointmentCost,
                'total_price' => $appointmentCost,
                'subtotal' => $appointmentCost,
            ]);

            $appointment = Appointment::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'customer_id' => $this->customerId,
                'hospital_id' => $this->hospitalId,
                'appointment_type' => $this->appointmentType,
                'patient_name' => $this->appointmentType === 'someone_else' ? $this->patientName : null,
                'appointment_time' => $this->appointmentDateTime,
                'status' => 'scheduled',
            ]);

            // Success - Redirect to orders page
            session()->flash('success', __('Payment successful! Appointment booked. Order #:number created', ['number' => $order->order_number]));
            return redirect()->route('admin.orders.show', $order);

        } catch (\Exception $e) {
            // Log the actual error
            \Log::error('Appointment booking exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            
            // Show user-friendly error
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => __('Booking Error'),
                'message' => $e->getMessage(), // Show actual error in development
            ]);
        }
    }

    public function render()
    {
        return view('livewire.appointment-booking-form');
    }
}
