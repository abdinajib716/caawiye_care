<x-layouts.backend-layout>
    <x-slot name="title">{{ $breadcrumbs['title'] }}</x-slot>

    <x-breadcrumbs :breadcrumbs="array_merge($breadcrumbs, ['show_messages_after' => false])" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Manage your service categories and organize your services effectively.') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-buttons.button
                    variant="primary"
                    as="a"
                    href="{{ route('admin.service-categories.create') }}"
                    icon="lucide:plus"
                >
                    {{ __('Add Category') }}
                </x-buttons.button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                            <iconify-icon icon="lucide:folder" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Total Categories') }}</div>
                        <div class="text-2xl font-bold text-gray-900" id="total-categories">{{ App\Models\ServiceCategory::count() }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                            <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('Active Categories') }}</div>
                        <div class="text-2xl font-bold text-gray-900" id="active-categories">{{ App\Models\ServiceCategory::active()->count() }}</div>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-orange-500 text-white">
                            <iconify-icon icon="lucide:briefcase" class="h-5 w-5"></iconify-icon>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">{{ __('With Services') }}</div>
                        <div class="text-2xl font-bold text-gray-900" id="categories-with-services">{{ App\Models\ServiceCategory::has('services')->count() }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Categories Datatable -->
        <x-card>
            <livewire:datatable.service-category-datatable />
        </x-card>
    </div>

    @push('scripts')
    <script>
        // Convert flash messages to toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.showToast('success', '{{ __("Success") }}', '{{ session("success") }}');
            @endif

            @if(session('error'))
                window.showToast('error', '{{ __("Error") }}', '{{ session("error") }}');
            @endif
        });
    </script>
    @endpush
</x-layouts.backend-layout>
