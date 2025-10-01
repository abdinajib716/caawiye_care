<x-layouts.backend-layout>
    <x-slot name="title">{{ $breadcrumbs['title'] }}</x-slot>

    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Create Category') }}
            </h2>
            <div class="flex items-center gap-3">
                <nav>
                    <ol class="flex items-center gap-1.5 pe-2">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                                {{ __("Home") }}
                                <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.service-categories.index') }}">
                                {{ __("Service Categories") }}
                                <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                            </a>
                        </li>
                        <li class="text-sm text-gray-700 dark:text-white/90">
                            {{ __('Create') }}
                        </li>
                    </ol>
                </nav>
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
        <x-messages />
    </x-slot>

    <div class="space-y-6">

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
