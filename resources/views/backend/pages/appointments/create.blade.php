<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Book New Appointment') }}</h1>
            <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Back to Appointments') }}
            </a>
        </div>

        <x-card>
            @livewire('appointment-booking-form')
        </x-card>
    </div>
</x-layouts.backend-layout>
