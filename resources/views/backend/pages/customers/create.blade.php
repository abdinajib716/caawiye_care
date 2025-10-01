<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card class="bg-white">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Create New Customer') }}</h3>
            </x-slot>

            <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Basic Information') }}</h4>

                        <!-- Customer Name -->
                        <x-inputs.input
                            name="name"
                            label="{{ __('Customer Name') }}"
                            placeholder="{{ __('Enter customer full name') }}"
                            required
                            :value="old('name')"
                        />

                        <!-- Phone Number -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                            <div class="flex space-x-2">
                                <!-- Country Code -->
                                <x-inputs.select
                                    name="country_code"
                                    :options="[
                                        '+252' => '🇸🇴 +252 (Somalia)',
                                        '+254' => '🇰🇪 +254 (Kenya)',
                                        '+251' => '🇪🇹 +251 (Ethiopia)',
                                        '+256' => '🇺🇬 +256 (Uganda)',
                                        '+255' => '🇹🇿 +255 (Tanzania)',
                                        '+1' => '🇺🇸 +1 (USA/Canada)',
                                        '+44' => '🇬🇧 +44 (UK)',
                                        '+971' => '🇦🇪 +971 (UAE)',
                                        '+966' => '🇸🇦 +966 (Saudi Arabia)',
                                        '+20' => '🇪🇬 +20 (Egypt)',
                                    ]"
                                    :value="old('country_code', '+252')"
                                    class="w-48"
                                    required
                                />

                                <!-- Phone Number -->
                                <x-inputs.input
                                    name="phone"
                                    placeholder="{{ __('Enter phone number') }}"
                                    required
                                    :value="old('phone')"
                                    class="flex-1"
                                />
                            </div>
                            @error('phone')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <x-inputs.textarea
                            name="address"
                            label="{{ __('Address') }}"
                            placeholder="{{ __('Enter customer address (optional)') }}"
                            rows="3"
                            :value="old('address')"
                        />
                    </div>

                    <!-- Settings -->
                    <div class="space-y-6">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Settings') }}</h4>

                        <!-- Status -->
                        <x-inputs.select
                            name="status"
                            label="{{ __('Status') }}"
                            :options="[
                                'active' => __('Active'),
                                'inactive' => __('Inactive')
                            ]"
                            required
                            :value="old('status', 'active')"
                        />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.customers.index') }}">
                        {{ __('Cancel') }}
                    </x-buttons.button>
                    <x-buttons.button variant="primary" type="submit">
                        <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Create Customer') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
