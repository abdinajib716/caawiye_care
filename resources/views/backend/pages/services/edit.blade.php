<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card class="bg-white">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Service') }}</h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($service->status === 'active') bg-green-100 text-green-800
                            @elseif($service->status === 'inactive') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $service->status_label }}
                        </span>
                        @if($service->is_featured)
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
                                <iconify-icon icon="lucide:star" class="mr-1 h-3 w-3"></iconify-icon>
                                {{ __('Featured') }}
                            </span>
                        @endif
                    </div>
                </div>
            </x-slot>

            <form action="{{ route('admin.services.update', $service) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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
                            :value="old('name', $service->name)"
                        />

                        <!-- Short Description -->
                        <x-inputs.textarea
                            name="short_description"
                            label="{{ __('Short Description') }}"
                            placeholder="{{ __('Brief description of the service') }}"
                            rows="3"
                            :value="old('short_description', $service->short_description)"
                        />



                        <!-- Category -->
                        <x-inputs.select
                            name="category_id"
                            label="{{ __('Category') }}"
                            :options="$categories->pluck('name', 'id')->toArray()"
                            placeholder="{{ __('Select a category') }}"
                            :value="old('category_id', $service->category_id)"
                        />
                    </div>

                    <!-- Pricing & Settings -->
                    <div class="space-y-6">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Pricing & Settings') }}</h4>

                        <!-- Current Profit Information -->
                        @if($service->cost > 0)
                            <div class="rounded-md bg-blue-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <iconify-icon icon="lucide:info" class="h-5 w-5 text-blue-400"></iconify-icon>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">{{ __('Current Profit Analysis') }}</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>{{ __('Profit Margin: :margin', ['margin' => $service->formatted_price]) }}</p>
                                            <p>{{ __('Profit Percentage: :percentage%', ['percentage' => $service->profit_percentage]) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Price -->
                        <x-inputs.input
                            name="price"
                            label="{{ __('Service Price') }}"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                            :value="old('price', $service->price)"
                        />

                        <!-- Cost -->
                        <x-inputs.input
                            name="cost"
                            label="{{ __('Service Cost') }}"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            :value="old('cost', $service->cost)"
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
                            :value="old('status', $service->status)"
                        />

                        <!-- Service Type -->
                        <x-inputs.select
                            name="service_type"
                            label="{{ __('Service Type') }}"
                            :options="[
                                'standard' => __('Standard Service'),
                                'appointment' => __('Appointment Service')
                            ]"
                            required
                            :value="old('service_type', $service->service_type)"
                            help="{{ __('Select appointment type if this service requires scheduling') }}"
                        />
                    </div>
                </div>

                <!-- Custom Fields Configuration -->
                <div class="border-t border-gray-200 pt-6" x-data="{ showCustomFields: {{ old('has_custom_fields', $service->has_custom_fields) ? 'true' : 'false' }} }">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-medium text-gray-900">{{ __('Custom Fields Configuration') }}</h4>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Configure additional fields required for this service') }}</p>
                        </div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="has_custom_fields"
                                value="1"
                                x-model="showCustomFields"
                                @checked(old('has_custom_fields', $service->has_custom_fields))
                                class="form-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">{{ __('Enable Custom Fields') }}</span>
                        </label>
                    </div>

                    <div x-show="showCustomFields" x-transition class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                            <div class="flex">
                                <iconify-icon icon="lucide:sparkles" class="mr-2 h-5 w-5 flex-shrink-0 text-blue-600"></iconify-icon>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">{{ __('Visual Form Builder') }}</p>
                                    <p class="mt-1 text-sm text-blue-700">
                                        {{ __('Build your custom fields visually - no coding required! Click "Add Field" or use a quick template.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Visual Form Builder Component -->
                        <x-form-builder
                            name="custom_fields_config"
                            :value="old('custom_fields_config', !empty($service->custom_fields_config) ? json_encode($service->custom_fields_config) : null)"
                        />
                    </div>
                </div>



                <!-- Service Information -->
                <div class="rounded-md bg-gray-50 p-4">
                    <div class="text-sm text-gray-600">
                        <p><strong>{{ __('Created:') }}</strong> {{ $service->created_at->format('M j, Y \a\t g:i A') }}</p>
                        <p><strong>{{ __('Last Updated:') }}</strong> {{ $service->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        <p><strong>{{ __('Slug:') }}</strong> {{ $service->slug }}</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.services.index') }}">
                        {{ __('Cancel') }}
                    </x-buttons.button>
                    <x-buttons.button variant="primary" type="submit">
                        <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Update Service') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
