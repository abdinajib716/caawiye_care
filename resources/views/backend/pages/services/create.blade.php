<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card class="bg-white">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Create New Service') }}</h3>
            </x-slot>

            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Basic Information') }}</h4>

                        <!-- Service Name -->
                        <x-inputs.input
                            name="name"
                            label="{{ __('Service Name') }}"
                            placeholder="{{ __('Enter service name') }}"
                            required
                            :value="old('name')"
                        />

                        <!-- Short Description -->
                        <x-inputs.textarea
                            name="short_description"
                            label="{{ __('Short Description') }}"
                            placeholder="{{ __('Brief description of the service') }}"
                            rows="3"
                            :value="old('short_description')"
                        />



                        <!-- Category -->
                        <x-inputs.select
                            name="category_id"
                            label="{{ __('Category') }}"
                            :options="$categories->pluck('name', 'id')->toArray()"
                            placeholder="{{ __('Select a category') }}"
                            :value="old('category_id')"
                        />
                    </div>

                    <!-- Pricing & Settings -->
                    <div class="space-y-6">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Pricing & Settings') }}</h4>

                        <!-- Price -->
                        <x-inputs.input
                            name="price"
                            label="{{ __('Service Price') }}"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                            :value="old('price')"
                        />

                        <!-- Cost -->
                        <x-inputs.input
                            name="cost"
                            label="{{ __('Service Cost') }}"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            :value="old('cost')"
                            help="{{ __('Optional: Cost for calculating profit margins') }}"
                        />



                        <!-- Status -->
                        <x-inputs.select
                            name="status"
                            label="{{ __('Status') }}"
                            :options="[
                                'active' => __('Active'),
                                'inactive' => __('Inactive'),
                                'discontinued' => __('Discontinued')
                            ]"
                            required
                            :value="old('status', 'active')"
                        />


                    </div>
                </div>



                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.services.index') }}">
                        {{ __('Cancel') }}
                    </x-buttons.button>
                    <x-buttons.button variant="primary" type="submit">
                        <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Create Service') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
