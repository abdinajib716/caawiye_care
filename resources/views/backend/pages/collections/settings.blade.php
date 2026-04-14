<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="max-w-2xl">
        <x-card class="bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                <iconify-icon icon="lucide:settings" class="inline mr-2"></iconify-icon>
                {{ __('Service Charge Configuration') }}
            </h3>

            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.collections.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">{{ __('Base Service Charge') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="service_charge" value="{{ old('service_charge', $charges['base_service_charge']) }}" 
                            class="form-control pl-8" step="0.01" min="0" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Fixed fee per report collection request') }}</p>
                    @error('service_charge') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Delivery Fee') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $charges['delivery_fee']) }}" 
                            class="form-control pl-8" step="0.01" min="0" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Applied only if delivery is required') }}</p>
                    @error('delivery_fee') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Pricing Formula') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ __('Total Amount') }}</strong> = {{ __('Base Service Charge') }} + {{ __('Delivery Fee (if delivery required)') }}
                    </p>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                    <div class="flex items-start">
                        <iconify-icon icon="lucide:alert-circle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5"></iconify-icon>
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            {{ __('Changes will only affect future requests. Existing requests will retain their original charges.') }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
