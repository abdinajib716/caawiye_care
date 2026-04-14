<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Book Lab Test') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Create a new lab test booking for a customer') }}</p>
    </div>

    <livewire:lab-test-booking-form />
</x-layouts.backend-layout>
