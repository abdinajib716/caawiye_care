<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Customer;
use App\Models\ScanImagingService;
use App\Services\ScanImagingService as ScanImagingBookingService;
use App\Services\WaafipayService;
use Livewire\Component;

class ScanImagingBookingForm extends Component
{
    // Step management
    public int $currentStep = 1;
    public int $totalSteps = 3;

    // Customer
    public ?int $customerId = null;
    public array $customers = [];
    public string $customerName = '';
    public string $customerPhone = '';
    public bool $showNewCustomerForm = false;

    // Patient information
    public string $patientName = '';

    // Service
    public ?int $serviceId = null;
    public array $services = [];
    public ?ScanImagingService $selectedService = null;

    // Appointment
    public string $appointmentTime = '';

    // Notes
    public string $notes = '';

    protected ScanImagingBookingService $bookingService;
    protected WaafipayService $waafipayService;

    public function boot(ScanImagingBookingService $bookingService, WaafipayService $waafipayService)
    {
        $this->bookingService = $bookingService;
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->services = ScanImagingService::with('provider')->orderBy('service_name')->get()->toArray();
        $this->appointmentTime = now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');
        // Load all customers for dropdown
        $this->customers = Customer::orderBy('name')->get()->toArray();
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
        $this->customers = Customer::orderBy('name')->get()->toArray();
        
        session()->flash('success', __('Customer created successfully'));
    }

    public function updatedServiceId()
    {
        if ($this->serviceId) {
            $this->selectedService = ScanImagingService::with('provider')->find($this->serviceId);
        } else {
            $this->selectedService = null;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep === 1 && !$this->customerId) {
            session()->flash('error', __('Please select a customer'));
            return;
        }

        if ($this->currentStep === 2 && !$this->serviceId) {
            session()->flash('error', __('Please select a service'));
            return;
        }

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

    public function submit()
    {
        try {
            // Get customer details
            $customer = Customer::find($this->customerId);
            if (!$customer) {
                session()->flash('error', __('Customer not found'));
                return;
            }

            // Get total amount for payment
            $totalAmount = $this->selectedService->total_with_commission;

            // Process payment through Waafi Pay
            $paymentResult = $this->waafipayService->processPayment([
                'phone' => $customer->phone,
                'amount' => $totalAmount,
                'customer_name' => $customer->name,
                'customer_id' => $customer->id,
                'description' => 'Scan & Imaging Booking',
                'currency' => 'USD',
            ]);

            \Log::info('Scan Imaging Payment result received', [
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
                
                session()->flash('error', $errorMessage);
                return;
            }

            if (!isset($paymentResult['success']) || $paymentResult['success'] !== true) {
                session()->flash('error', __('Payment verification failed'));
                return;
            }

            \Log::info('Payment CONFIRMED successful (responseCode: 2001), creating scan imaging booking');

            $data = [
                'customer_id' => $this->customerId,
                'patient_name' => $this->patientName ?: $customer->name,
                'scan_imaging_service_id' => $this->serviceId,
                'appointment_time' => $this->appointmentTime,
                'notes' => $this->notes,
                'payment_method' => 'mobile_money',
                'payment_reference' => $paymentResult['transaction']->reference_id ?? null,
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ];

            $booking = $this->bookingService->createBooking($data);

            session()->flash('success', __('Payment successful! Scan & imaging booking created. Booking #:number', ['number' => $booking->booking_number]));
            return redirect()->route('admin.scan-imaging-bookings.show', $booking);
        } catch (\Exception $e) {
            \Log::error('Scan imaging booking error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.scan-imaging-booking-form');
    }
}
