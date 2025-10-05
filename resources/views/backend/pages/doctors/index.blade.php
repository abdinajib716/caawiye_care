<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Doctors Management -->
        <x-card class="bg-white">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Doctors Management') }}</h3>
            </x-slot>

            <!-- Doctors Datatable -->
            <livewire:datatable.doctor-datatable />
        </x-card>
    </div>
</x-layouts.backend-layout>

