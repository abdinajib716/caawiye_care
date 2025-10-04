<?php

declare(strict_types=1);

namespace App\Livewire\OrderZone;

use App\Models\Hospital;
use App\Models\Service;
use App\Services\ServiceFieldDataService;
use Livewire\Component;
use Livewire\Attributes\On;

class ServiceDetailsStep extends Component
{
    public array $services = [];
    public array $fieldData = [];
    public array $validationErrors = [];
    public bool $hasCustomFieldServices = false;

    protected ServiceFieldDataService $fieldDataService;

    public function boot(ServiceFieldDataService $fieldDataService)
    {
        $this->fieldDataService = $fieldDataService;
    }

    public function mount()
    {
        $this->fieldData = [];
        $this->validationErrors = [];
    }

    #[On('services-updated')]
    public function handleServicesUpdated($data)
    {
        $this->services = $data['services'] ?? [];
        $this->checkForCustomFields();
        
        // Initialize field data for new services
        foreach ($this->services as $service) {
            $serviceModel = Service::find($service['id']);
            if ($serviceModel && $serviceModel->hasCustomFields()) {
                $fields = $serviceModel->getCustomFields();
                foreach ($fields as $field) {
                    $key = $service['id'] . '_' . $field['key'];
                    if (!isset($this->fieldData[$key])) {
                        $this->fieldData[$key] = $field['default_value'] ?? '';
                    }
                }
            }
        }
    }

    public function checkForCustomFields()
    {
        $this->hasCustomFieldServices = false;
        
        foreach ($this->services as $service) {
            $serviceModel = Service::find($service['id']);
            if ($serviceModel && $serviceModel->hasCustomFields()) {
                $this->hasCustomFieldServices = true;
                break;
            }
        }
    }

    public function validateAndProceed()
    {
        $this->validationErrors = [];
        $allValid = true;

        foreach ($this->services as $service) {
            $serviceModel = Service::find($service['id']);

            if ($serviceModel && $serviceModel->hasCustomFields()) {
                // Extract field data for this service
                $serviceFieldData = [];
                $fields = $serviceModel->getCustomFields();

                foreach ($fields as $field) {
                    $key = $service['id'] . '_' . $field['key'];
                    $serviceFieldData[$field['key']] = $this->fieldData[$key] ?? null;
                }

                // Validate field data
                try {
                    $this->fieldDataService->validateFieldData($serviceModel, $serviceFieldData);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $allValid = false;
                    foreach ($e->errors() as $fieldKey => $messages) {
                        $key = $service['id'] . '_' . $fieldKey;
                        $this->validationErrors[$key] = $messages[0];
                    }
                }
            }
        }

        if ($allValid) {
            // Prepare field data for each service
            $servicesWithFieldData = [];
            foreach ($this->services as $service) {
                $serviceModel = Service::find($service['id']);
                $serviceData = $service;
                
                if ($serviceModel && $serviceModel->hasCustomFields()) {
                    $serviceFieldData = [];
                    $fields = $serviceModel->getCustomFields();
                    
                    foreach ($fields as $field) {
                        $key = $service['id'] . '_' . $field['key'];
                        $serviceFieldData[$field['key']] = $this->fieldData[$key] ?? null;
                    }
                    
                    $serviceData['field_data'] = $serviceFieldData;
                    $serviceData['has_custom_fields'] = true;
                }
                
                $servicesWithFieldData[] = $serviceData;
            }

            $this->dispatch('service-details-completed', [
                'services' => $servicesWithFieldData
            ]);
        }
    }

    public function shouldShowField($serviceId, $field)
    {
        if (empty($field['show_if'])) {
            return true;
        }

        $showIfField = $field['show_if']['field'];
        $showIfValue = $field['show_if']['value'];
        $key = $serviceId . '_' . $showIfField;

        return isset($this->fieldData[$key]) && $this->fieldData[$key] == $showIfValue;
    }

    #[On('clear-order')]
    public function clearData()
    {
        $this->services = [];
        $this->fieldData = [];
        $this->validationErrors = [];
        $this->hasCustomFieldServices = false;
    }

    public function render()
    {
        $hospitals = Hospital::active()->orderBy('name')->get();

        return view('livewire.order-zone.service-details-step', [
            'hospitals' => $hospitals,
        ]);
    }
}

