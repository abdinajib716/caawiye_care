<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Customer;
use App\Models\LabTest;
use App\Models\User;
use App\Services\LabTestBookingService;
use App\Services\WaafipayService;
use Livewire\Component;

class LabTestBookingForm extends Component
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
    public string $patientAddress = '';

    // Lab tests
    public array $selectedTests = [];
    public array $labTests = [];

    // Nurse
    public ?int $nurseId = null;
    public array $nurses = [];

    // Notes
    public string $notes = '';

    // Totals
    public float $totalCost = 0;
    public float $totalCommission = 0;
    public float $totalAmount = 0;
    public float $billCustomerCommission = 0;
    public float $billProviderCommission = 0;

    protected LabTestBookingService $bookingService;
    protected WaafipayService $waafipayService;

    public function boot(LabTestBookingService $bookingService, WaafipayService $waafipayService)
    {
        $this->bookingService = $bookingService;
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->labTests = LabTest::with('provider')->orderBy('name')->get()->toArray();
        // Get users who can be assigned as nurses (users with appropriate permissions)
        $this->nurses = User::orderBy('first_name')->get()->toArray();
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

    public function toggleTest(int $testId)
    {
        if (in_array($testId, $this->selectedTests)) {
            $this->selectedTests = array_values(array_diff($this->selectedTests, [$testId]));
        } else {
            $this->selectedTests[] = $testId;
        }
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $tests = LabTest::whereIn('id', $this->selectedTests)->get();
        
        $this->totalCost = $tests->sum('cost');
        $this->totalCommission = $tests->sum('commission_amount');
        
        // Calculate commission by type
        $this->billCustomerCommission = $tests->where('commission_type', 'bill_customer')->sum('commission_amount');
        $this->billProviderCommission = $tests->where('commission_type', 'bill_provider')->sum('commission_amount');
        
        // Total amount: cost + commission only for bill_customer tests
        $this->totalAmount = $this->totalCost + $this->billCustomerCommission;
    }

    public function nextStep()
    {
        if ($this->currentStep === 1 && !$this->customerId) {
            session()->flash('error', __('Please select a customer'));
            return;
        }

        if ($this->currentStep === 2 && empty($this->selectedTests)) {
            session()->flash('error', __('Please select at least one lab test'));
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

            // Calculate total amount for payment
            $totalAmount = $this->totalAmount;

            // Process payment through Waafi Pay
            $paymentResult = $this->waafipayService->processPayment([
                'phone' => $customer->phone,
                'amount' => $totalAmount,
                'customer_name' => $customer->name,
                'customer_id' => $customer->id,
                'description' => 'Lab Test Booking',
                'currency' => 'USD',
            ]);

            \Log::info('Lab Test Payment result received', [
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

            \Log::info('Payment CONFIRMED successful (responseCode: 2001), creating lab test booking');

            // Build items array with detailed cost info for each selected test
            $tests = LabTest::whereIn('id', $this->selectedTests)->get();
            $items = [];
            
            foreach ($tests as $test) {
                $cost = (float) $test->cost;
                $commissionPercentage = (float) $test->commission_percentage;
                $commissionAmount = (float) $test->commission_amount;
                $commissionType = $test->commission_type ?? 'bill_customer';
                
                // Calculate total based on commission type
                if ($commissionType === 'bill_customer') {
                    $total = $cost + $commissionAmount;
                } else {
                    $total = $cost;
                }
                
                // Profit is the commission amount
                $profit = $commissionAmount;
                
                $items[] = [
                    'lab_test_id' => $test->id,
                    'cost' => $cost,
                    'commission_percentage' => $commissionPercentage,
                    'commission_type' => $commissionType,
                    'commission_amount' => $commissionAmount,
                    'profit' => $profit,
                    'total' => $total,
                ];
            }

            $data = [
                'customer_id' => $this->customerId,
                'patient_name' => $this->patientName ?: $customer->name,
                'patient_address' => $this->patientAddress,
                'assigned_nurse_id' => $this->nurseId,
                'notes' => $this->notes,
                'items' => $items,
                'payment_method' => 'mobile_money',
                'payment_reference' => $paymentResult['transaction']->reference_id ?? null,
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ];

            $booking = $this->bookingService->createBooking($data);

            session()->flash('success', __('Payment successful! Lab test booking created. Booking #:number', ['number' => $booking->booking_number]));
            return redirect()->route('admin.lab-test-bookings.show', $booking);
        } catch (\Exception $e) {
            \Log::error('Lab test booking error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lab-test-booking-form');
    }
}
