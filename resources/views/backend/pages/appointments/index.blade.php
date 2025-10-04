<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Appointments') }}</h1>
        </div>

        <!-- Datatable -->
        <x-card>
            @livewire('datatable.appointment-datatable')
        </x-card>
    </div>
</x-layouts.backend-layout>

