<x-layouts.backend-layout>
    <x-slot name="title">{{ $breadcrumbs['title'] }}</x-slot>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Update the category information.') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <x-buttons.button
                    variant="secondary"
                    as="a"
                    href="{{ route('admin.service-categories.show', $category) }}"
                    icon="lucide:eye"
                >
                    {{ __('View Category') }}
                </x-buttons.button>

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

        <!-- Edit Form -->
        <x-card>
            <form action="{{ route('admin.service-categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Category Name -->
                        <x-inputs.input
                            name="name"
                            label="{{ __('Category Name') }}"
                            placeholder="{{ __('Enter category name') }}"
                            required
                            :value="old('name', $category->name)"
                        />

                        <!-- Status -->
                        <x-inputs.checkbox
                            name="is_active"
                            label="{{ __('Active') }}"
                            value="1"
                            :checked="old('is_active', $category->is_active)"
                            help="{{ __('Only active categories will be available for selection when creating services.') }}"
                        />
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">

                        <!-- Category Stats -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 dark:bg-gray-800 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                {{ __('Category Statistics') }}
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Total Services:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $category->services_count ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Active Services:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $category->active_services_count ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Subcategories:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $category->children->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Created:') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $category->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($category->children->count() > 0 || $category->services_count > 0)
                        <!-- Warning Box -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 dark:bg-yellow-900/20 dark:border-yellow-800">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <iconify-icon icon="lucide:alert-triangle" class="w-5 h-5 text-yellow-400"></iconify-icon>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        {{ __('Important Notice') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        @if($category->services_count > 0)
                                            <p>{{ __('This category contains :count services. Changes may affect service organization.', ['count' => $category->services_count]) }}</p>
                                        @endif
                                        @if($category->children->count() > 0)
                                            <p>{{ __('This category has :count subcategories. Deactivating it may affect the subcategories.', ['count' => $category->children->count()]) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-buttons.button
                        variant="secondary"
                        as="a"
                        href="{{ route('admin.service-categories.show', $category) }}"
                    >
                        {{ __('Cancel') }}
                    </x-buttons.button>

                    <x-buttons.button
                        variant="primary"
                        type="submit"
                        icon="lucide:save"
                    >
                        {{ __('Update Category') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>

</x-layouts.backend-layout>
