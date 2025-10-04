<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories
        $medicalCategory = ServiceCategory::where('name', 'Medical Services')->first();
        $consultationCategory = ServiceCategory::where('name', 'Consultations')->first();
        $reportsCategory = ServiceCategory::where('name', 'Reports & Analysis')->first();
        $emergencyCategory = ServiceCategory::where('name', 'Emergency Services')->first();
        $labCategory = ServiceCategory::where('name', 'Laboratory Tests')->first();

        // Create predefined services
        $services = [
            // Medical Services
            [
                'name' => 'Blood Test Analysis',
                'slug' => 'blood-test-analysis',
                'description' => 'Comprehensive blood work analysis including CBC, chemistry panel, and lipid profile with detailed interpretation and recommendations.',
                'short_description' => 'Complete blood analysis with detailed report',
                'price' => 75.00,
                'cost' => 25.00,
                'category_id' => $labCategory?->id ?? $medicalCategory?->id,
                'sku' => 'SRV-BTA001',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Medical Report Review',
                'slug' => 'medical-report-review',
                'description' => 'Professional review and analysis of medical reports by qualified healthcare professionals with expert recommendations.',
                'short_description' => 'Expert medical report analysis and consultation',
                'price' => 150.00,
                'cost' => 50.00,
                'category_id' => $reportsCategory?->id,
                'sku' => 'SRV-MRR001',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'X-Ray Interpretation',
                'slug' => 'xray-interpretation',
                'description' => 'Professional interpretation of X-ray images with detailed findings report and clinical recommendations.',
                'short_description' => 'Expert X-ray analysis and reporting',
                'price' => 120.00,
                'cost' => 40.00,
                'category_id' => $medicalCategory?->id,
                'sku' => 'SRV-XRI001',
                'status' => 'active',
            ],
            [
                'name' => 'Health Consultation',
                'slug' => 'health-consultation',
                'description' => 'One-on-one health consultation with certified healthcare professionals including health assessment and personalized recommendations.',
                'short_description' => 'Personal health consultation session',
                'price' => 200.00,
                'cost' => 80.00,
                'category_id' => $consultationCategory?->id,
                'sku' => 'SRV-HC001',
                'status' => 'active',
            ],
            [
                'name' => 'Emergency Medical Review',
                'slug' => 'emergency-medical-review',
                'description' => 'Urgent medical report review for emergency cases with priority processing and immediate consultation availability.',
                'short_description' => 'Priority emergency medical consultation',
                'price' => 300.00,
                'cost' => 100.00,
                'category_id' => $emergencyCategory?->id,
                'sku' => 'SRV-EMR001',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Urine Analysis',
                'slug' => 'urine-analysis',
                'description' => 'Complete urinalysis including microscopic examination and chemical analysis with clinical interpretation.',
                'short_description' => 'Comprehensive urine test analysis',
                'price' => 45.00,
                'cost' => 15.00,
                'category_id' => $labCategory?->id ?? $medicalCategory?->id,
                'sku' => 'SRV-UA001',
                'status' => 'active',
            ],
            [
                'name' => 'ECG Interpretation',
                'slug' => 'ecg-interpretation',
                'description' => 'Professional electrocardiogram interpretation with detailed cardiac assessment and recommendations.',
                'short_description' => 'Expert ECG analysis and reporting',
                'price' => 90.00,
                'cost' => 30.00,
                'category_id' => $medicalCategory?->id,
                'sku' => 'SRV-ECG001',
                'status' => 'active',
            ],
            [
                'name' => 'Specialist Consultation',
                'slug' => 'specialist-consultation',
                'description' => 'Consultation with medical specialists for complex cases requiring expert opinion and specialized care recommendations.',
                'short_description' => 'Specialized medical consultation',
                'price' => 350.00,
                'cost' => 120.00,
                'category_id' => $consultationCategory?->id,
                'sku' => 'SRV-SC001',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Health Screening Package',
                'slug' => 'health-screening-package',
                'description' => 'Comprehensive health screening package including multiple tests and consultations for complete health assessment.',
                'short_description' => 'Complete health screening package',
                'price' => 450.00,
                'cost' => 150.00,
                'category_id' => $medicalCategory?->id,
                'sku' => 'SRV-HSP001',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Telemedicine Consultation',
                'slug' => 'telemedicine-consultation',
                'description' => 'Remote medical consultation via video call with qualified healthcare professionals for convenient healthcare access.',
                'short_description' => 'Remote video consultation service',
                'price' => 125.00,
                'cost' => 45.00,
                'category_id' => $consultationCategory?->id,
                'sku' => 'SRV-TC001',
                'status' => 'active',
            ],
            [
                'name' => 'Doctor Appointment',
                'slug' => 'doctor-appointment',
                'short_description' => 'Schedule an appointment with a doctor at your preferred hospital',
                'price' => 50.00,
                'cost' => 20.00,
                'category_id' => $consultationCategory?->id,
                'status' => 'active',
                'is_featured' => true,
                'service_type' => 'appointment',
                'has_custom_fields' => true,
                'custom_fields_config' => [
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
                            'default_value' => 'self',
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
                            'label' => 'Hospital',
                            'type' => 'select',
                            'required' => true,
                            'data_source' => 'hospitals',
                        ],
                        [
                            'key' => 'appointment_time',
                            'label' => 'Appointment Time',
                            'type' => 'datetime',
                            'required' => true,
                            'validation' => 'future',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }

        // Create additional random services for testing
        Service::factory()->count(15)->create();
    }
}
