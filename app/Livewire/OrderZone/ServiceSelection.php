<?php

declare(strict_types=1);

namespace App\Livewire\OrderZone;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\On;

class ServiceSelection extends Component
{
    public string $search = '';
    public array $selectedServices = [];
    public array $quantities = [];

    public function mount()
    {
        // Initialize with empty arrays
        $this->selectedServices = [];
        $this->quantities = [];
    }

    public function updatedSearch()
    {
        // Real-time search - no action needed, will re-render
    }

    public function toggleService($serviceId)
    {
        if (in_array($serviceId, $this->selectedServices)) {
            // Remove service
            $this->selectedServices = array_values(array_diff($this->selectedServices, [$serviceId]));
            unset($this->quantities[$serviceId]);
        } else {
            // Add service
            $this->selectedServices[] = $serviceId;
            $this->quantities[$serviceId] = 1;
        }

        $this->dispatch('services-updated', [
            'services' => $this->getSelectedServicesData()
        ]);
    }

    public function updateQuantity($serviceId, $quantity)
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        $this->quantities[$serviceId] = $quantity;

        $this->dispatch('services-updated', [
            'services' => $this->getSelectedServicesData()
        ]);
    }

    public function incrementQuantity($serviceId)
    {
        $this->quantities[$serviceId] = ($this->quantities[$serviceId] ?? 1) + 1;

        $this->dispatch('services-updated', [
            'services' => $this->getSelectedServicesData()
        ]);
    }

    public function decrementQuantity($serviceId)
    {
        if (($this->quantities[$serviceId] ?? 1) > 1) {
            $this->quantities[$serviceId]--;

            $this->dispatch('services-updated', [
                'services' => $this->getSelectedServicesData()
            ]);
        }
    }

    public function getSelectedServicesData()
    {
        $services = Service::whereIn('id', $this->selectedServices)->get();
        $data = [];

        foreach ($services as $service) {
            $quantity = $this->quantities[$service->id] ?? 1;
            $data[] = [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'quantity' => $quantity,
                'total' => $service->price * $quantity,
                'has_custom_fields' => $service->hasCustomFields(),
                'service_type' => $service->service_type,
            ];
        }

        return $data;
    }

    #[On('clear-order')]
    public function clearSelection()
    {
        $this->selectedServices = [];
        $this->quantities = [];
        $this->search = '';

        $this->dispatch('services-updated', [
            'services' => []
        ]);
    }

    public function render()
    {
        $services = Service::active()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->orderBy('name')
            ->get();

        $subtotal = 0;
        foreach ($this->selectedServices as $serviceId) {
            $service = $services->firstWhere('id', $serviceId);
            if ($service) {
                $quantity = $this->quantities[$serviceId] ?? 1;
                $subtotal += $service->price * $quantity;
            }
        }

        return view('livewire.order-zone.service-selection', [
            'services' => $services,
            'subtotal' => $subtotal,
        ]);
    }
}
