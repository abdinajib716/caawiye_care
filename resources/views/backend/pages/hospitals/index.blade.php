<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Hospitals Management -->
        <x-card class="bg-white">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Hospitals Management') }}</h3>
            </x-slot>

            <!-- Hospitals Datatable -->
            <livewire:datatable.hospital-datatable lazy />
        </x-card>
    </div>

    <x-import-modal
        title="{{ __('Import Hospitals') }}"
        :instructions="['Download the sample template below', 'Fill in your hospital data', 'Name and Email must be unique', 'Upload the completed CSV file']"
        :sampleTemplateUrl="route('admin.hospitals.sample-template')"
        :importUrl="route('admin.hospitals.import')"
        :requiredFields="['Name: Required, unique', 'Email: Required, unique', 'Phone: Required', 'Address: Required', 'Status: Required (active/inactive)']"
    />
</x-layouts.backend-layout>

