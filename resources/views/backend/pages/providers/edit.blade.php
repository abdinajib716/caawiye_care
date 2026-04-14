<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Provider') }}</h3>
        </x-slot>

        <form method="POST" action="{{ route('admin.providers.update', $provider) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-inputs.input
                    name="name"
                    label="{{ __('Provider Name') }}"
                    placeholder="{{ __('Enter provider name') }}"
                    required
                    :value="old('name', $provider->name)"
                />

                <x-inputs.input
                    name="phone"
                    label="{{ __('Phone Number') }}"
                    placeholder="{{ __('Enter phone number') }}"
                    required
                    :value="old('phone', $provider->phone)"
                />

                <x-inputs.input
                    name="email"
                    type="email"
                    label="{{ __('Email Address') }}"
                    placeholder="{{ __('Enter email address') }}"
                    :value="old('email', $provider->email)"
                />

                <x-inputs.textarea
                    name="address"
                    label="{{ __('Address') }}"
                    placeholder="{{ __('Enter provider address') }}"
                    rows="3"
                    :value="old('address', $provider->address)"
                />

                <x-inputs.select
                    name="status"
                    label="{{ __('Status') }}"
                    :options="[
                        'active' => __('Active'),
                        'inactive' => __('Inactive')
                    ]"
                    required
                    :value="old('status', $provider->status)"
                />

            </div>

            <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                <x-buttons.button variant="secondary" as="a" href="{{ route('admin.providers.index') }}">
                    {{ __('Cancel') }}
                </x-buttons.button>
                <x-buttons.button variant="primary" type="submit">
                    <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Update Provider') }}
                </x-buttons.button>
            </div>
        </form>
    </x-card>
</x-layouts.backend-layout>
