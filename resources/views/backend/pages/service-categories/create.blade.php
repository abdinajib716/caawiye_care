<x-layouts.backend-layout>
    <x-slot name="title">{{ $breadcrumbs['title'] }}</x-slot>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Category') }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Add a new service category to organize your services.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <x-buttons.button
                    variant="secondary"
                    as="a"
                    href="{{ route('admin.service-categories.index') }}"
                    icon="lucide:arrow-left"
                >
                    {{ __('Back to Categories') }}
                </x-buttons.button>
            </div>
        </div>

        <!-- Create Form -->
        <x-card>
            <form action="{{ route('admin.service-categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-6">
                    <!-- Category Name -->
                    <x-inputs.input
                        name="name"
                        label="{{ __('Category Name') }}"
                        placeholder="{{ __('Enter category name') }}"
                        required
                        :value="old('name')"
                    />

                    <!-- Status -->
                    <x-inputs.checkbox
                        name="is_active"
                        label="{{ __('Active') }}"
                        value="1"
                        :checked="old('is_active', true)"
                        help="{{ __('Only active categories will be available for selection when creating services.') }}"
                    />
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-buttons.button
                        variant="secondary"
                        as="a"
                        href="{{ route('admin.service-categories.index') }}"
                    >
                        {{ __('Cancel') }}
                    </x-buttons.button>

                    <x-buttons.button
                        variant="primary"
                        type="submit"
                        icon="lucide:save"
                    >
                        {{ __('Create Category') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>

</x-layouts.backend-layout>
