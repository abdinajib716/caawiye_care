<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Hospitals') }}</h1>
            @can('hospital.create')
                <x-buttons.button variant="primary" as="a" href="{{ route('admin.hospitals.create') }}">
                    <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Add Hospital') }}
                </x-buttons.button>
            @endcan
        </div>

        <!-- Datatable -->
        <x-card>
            @livewire('datatable.hospital-datatable')
        </x-card>
    </div>
</x-layouts.backend-layout>

