<?php

/**
 * Quick fix script to update appointment service with proper JSON configuration
 * Run: php fix_appointment_service.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Service;

// Find the service you created (adjust the name if needed)
$service = Service::where('name', 'appointment services')->first();

if (!$service) {
    echo "❌ Service 'appointment services' not found.\n";
    echo "Available services with custom fields enabled:\n";
    Service::where('has_custom_fields', true)->get()->each(function($s) {
        echo "  - {$s->name} (ID: {$s->id})\n";
    });
    exit(1);
}

echo "Found service: {$service->name} (ID: {$service->id})\n";
echo "Current config: " . json_encode($service->custom_fields_config) . "\n\n";

// Update with proper configuration
$config = [
    'fields' => [
        [
            'key' => 'appointment_type',
            'label' => 'Appointment Type',
            'type' => 'select',
            'required' => true,
            'options' => [
                ['value' => 'self', 'label' => 'Self'],
                ['value' => 'someone_else', 'label' => 'Someone Else'],
            ],
        ],
        [
            'key' => 'patient_name',
            'label' => 'Patient Name',
            'type' => 'text',
            'required' => true,
            'show_if' => [
                'field' => 'appointment_type',
                'value' => 'someone_else',
            ],
        ],
        [
            'key' => 'hospital_id',
            'label' => 'Select Hospital',
            'type' => 'select',
            'required' => true,
            'data_source' => 'hospitals',
        ],
        [
            'key' => 'appointment_time',
            'label' => 'Appointment Date & Time',
            'type' => 'datetime',
            'required' => true,
            'validation' => 'future',
        ],
    ],
];

$service->custom_fields_config = $config;
$service->service_type = 'appointment';
$service->save();

echo "✅ Service updated successfully!\n";
echo "New config: " . json_encode($service->custom_fields_config, JSON_PRETTY_PRINT) . "\n\n";
echo "Now go to Order Zone and select this service. You should see the custom fields step!\n";

