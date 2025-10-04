<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Hospital') }}</h3>
            </x-slot>

            <form action="{{ route('admin.hospitals.update', $hospital) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-6">
                        <x-inputs.input
                            name="name"
                            label="{{ __('Hospital Name') }}"
                            placeholder="{{ __('Enter hospital name') }}"
                            required
                            :value="old('name', $hospital->name)"
                        />

                        <x-inputs.input
                            name="phone"
                            label="{{ __('Phone Number') }}"
                            placeholder="{{ __('Enter phone number') }}"
                            :value="old('phone', $hospital->phone)"
                        />

                        <x-inputs.input
                            name="email"
                            label="{{ __('Email Address') }}"
                            type="email"
                            placeholder="{{ __('Enter email address') }}"
                            :value="old('email', $hospital->email)"
                        />
                    </div>

                    <div class="space-y-6">
                        <x-inputs.textarea
                            name="address"
                            label="{{ __('Address') }}"
                            placeholder="{{ __('Enter hospital address') }}"
                            rows="3"
                            :value="old('address', $hospital->address)"
                        />

                        <x-inputs.select
                            name="status"
                            label="{{ __('Status') }}"
                            :options="[
                                'active' => __('Active'),
                                'inactive' => __('Inactive')
                            ]"
                            required
                            :value="old('status', $hospital->status)"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.hospitals.index') }}">
                        {{ __('Cancel') }}
                    </x-buttons.button>
                    <x-buttons.button variant="primary" type="submit">
                        <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Update Hospital') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>

