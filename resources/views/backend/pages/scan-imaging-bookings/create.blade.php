<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Book Scan & Imaging') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Create a new scan & imaging booking for a customer') }}</p>
    </div>

    <livewire:scan-imaging-booking-form />
</x-layouts.backend-layout>
