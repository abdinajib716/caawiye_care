<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Scan & Imaging Service') }}</h3>
        </x-slot>

        <form method="POST" action="{{ route('admin.scan-imaging-services.update', $scanImagingService) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-inputs.input
                    name="service_name"
                    label="{{ __('Service Name') }}"
                    placeholder="{{ __('Enter service name') }}"
                    required
                    :value="old('service_name', $scanImagingService->service_name)"
                />

                <div>
                    <label class="form-label" for="provider_id">{{ __('Provider') }} <span class="text-red-500">*</span></label>
                    <select name="provider_id" id="provider_id" required class="form-control">
                        <option value="">{{ __('Select Provider') }}</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('provider_id', $scanImagingService->provider_id) == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('provider_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-inputs.input
                    name="cost"
                    type="number"
                    label="{{ __('Cost ($)') }}"
                    placeholder="{{ __('Enter cost') }}"
                    step="0.01"
                    min="0"
                    required
                    :value="old('cost', $scanImagingService->cost)"
                />

                <x-inputs.input
                    name="commission_percentage"
                    type="number"
                    label="{{ __('Commission (%)') }}"
                    placeholder="{{ __('Enter commission percentage') }}"
                    step="0.01"
                    min="0"
                    max="100"
                    required
                    :value="old('commission_percentage', $scanImagingService->commission_percentage)"
                />

                <div>
                    <label class="form-label" for="commission_type">{{ __('Commission Type') }} <span class="text-red-500">*</span></label>
                    <select name="commission_type" id="commission_type" required class="form-control">
                        <option value="bill_provider" {{ old('commission_type', $scanImagingService->commission_type) === 'bill_provider' ? 'selected' : '' }}>{{ __('Bill Provider (Deduct from Provider)') }}</option>
                        <option value="bill_customer" {{ old('commission_type', $scanImagingService->commission_type) === 'bill_customer' ? 'selected' : '' }}>{{ __('Bill Customer (Add to Customer)') }}</option>
                    </select>
                    @error('commission_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-inputs.select
                    name="status"
                    label="{{ __('Status') }}"
                    :options="[
                        'active' => __('Active'),
                        'inactive' => __('Inactive')
                    ]"
                    required
                    :value="old('status', $scanImagingService->status)"
                />

            </div>

            <div>
                <x-inputs.textarea
                    name="description"
                    label="{{ __('Description') }}"
                    placeholder="{{ __('Enter service description (optional)') }}"
                    rows="3"
                    :value="old('description', $scanImagingService->description)"
                />
            </div>

            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                <x-buttons.button variant="secondary" as="a" href="{{ route('admin.scan-imaging-services.index') }}">
                    {{ __('Cancel') }}
                </x-buttons.button>
                <x-buttons.button variant="primary" type="submit">
                    <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Update Service') }}
                </x-buttons.button>
            </div>
        </form>
    </x-card>
</x-layouts.backend-layout>
