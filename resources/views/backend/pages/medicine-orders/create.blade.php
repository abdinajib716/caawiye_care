<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Book Medicine Collection') }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Request medicine collection from suppliers') }}</p>
        </div>

        @livewire('medicine-booking-form')
    </div>
</x-layouts.backend-layout>
