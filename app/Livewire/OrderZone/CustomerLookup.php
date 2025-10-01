<?php

declare(strict_types=1);

namespace App\Livewire\OrderZone;

use App\Models\Customer;
use App\Services\WaafipayService;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomerLookup extends Component
{
    public string $search = '';
    public string $phone = '';
    public string $name = '';
    public string $address = '';
    public ?int $customerId = null;
    public ?string $detectedProvider = null;
    public bool $showNewCustomerForm = false;
    public bool $customerFound = false;
    public array $matchingCustomers = [];

    protected WaafipayService $waafipayService;

    public function boot(WaafipayService $waafipayService)
    {
        $this->waafipayService = $waafipayService;
    }

    public function mount()
    {
        $this->reset(['search', 'phone', 'name', 'address', 'customerId', 'detectedProvider', 'showNewCustomerForm', 'customerFound', 'matchingCustomers']);
    }

    public function updatedSearch()
    {
        $this->searchCustomers();
    }

    public function searchCustomers()
    {
        if (empty($this->search)) {
            $this->matchingCustomers = [];
            return;
        }

        // Search for customers by phone or name
        $this->matchingCustomers = Customer::where(function ($query) {
            $query->where('phone', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
        })
        ->limit(10)
        ->get()
        ->toArray();
    }

    public function toggleNewCustomerForm()
    {
        $this->showNewCustomerForm = !$this->showNewCustomerForm;

        if ($this->showNewCustomerForm) {
            // Clear search and matching customers when showing form
            $this->matchingCustomers = [];
            // Pre-fill phone if search looks like a phone number
            if (preg_match('/^[0-9+]+$/', $this->search)) {
                $this->phone = $this->search;
                $this->detectProvider();
            }
        } else {
            // Clear form when hiding
            $this->reset(['phone', 'name', 'address', 'detectedProvider']);
        }
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);

        if ($customer) {
            $this->customerFound = true;
            $this->showNewCustomerForm = false;
            $this->matchingCustomers = [];
            $this->customerId = $customer->id;
            $this->phone = $customer->phone;
            $this->name = $customer->name;
            $this->address = $customer->address ?? '';
            $this->search = $customer->name . ' (' . $customer->phone . ')';

            // Detect provider from selected customer's phone
            $this->detectProvider();

            $this->dispatch('customer-updated', [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                ]
            ]);
        }
    }

    public function detectProvider()
    {
        if (empty($this->phone)) {
            $this->detectedProvider = null;
            return;
        }

        $this->detectedProvider = $this->waafipayService->getProviderFromPhone($this->phone);

        $this->dispatch('provider-detected', [
            'provider' => $this->detectedProvider
        ]);
    }

    public function saveCustomer()
    {
        $this->validate([
            'phone' => 'required|string|min:9',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $formattedPhone = $this->waafipayService->formatPhoneNumber($this->phone);

        $customer = Customer::create([
            'phone' => $formattedPhone,
            'name' => $this->name,
            'address' => $this->address,
            'country_code' => '252',
        ]);

        $this->customerId = $customer->id;
        $this->customerFound = true;
        $this->showNewCustomerForm = false;
        $this->search = $customer->name . ' (' . $customer->phone . ')';

        $this->dispatch('customer-updated', [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
            ]
        ]);

        session()->flash('customer-saved', 'Customer created successfully!');
    }

    #[On('clear-order')]
    public function clearCustomer()
    {
        $this->reset(['search', 'phone', 'name', 'address', 'customerId', 'detectedProvider', 'showNewCustomerForm', 'customerFound', 'matchingCustomers']);

        $this->dispatch('customer-updated', ['customer' => null]);
        $this->dispatch('provider-detected', ['provider' => null]);
    }

    public function render()
    {
        return view('livewire.order-zone.customer-lookup');
    }
}
