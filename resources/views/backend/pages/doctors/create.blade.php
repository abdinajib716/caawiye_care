<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Create New Doctor') }}</h3>
            </x-slot>

            <form action="{{ route('admin.doctors.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="space-y-6">
                        <x-inputs.input
                            name="name"
                            label="{{ __('Doctor Name') }}"
                            placeholder="{{ __('Enter doctor name') }}"
                            required
                            :value="old('name')"
                        />

                        <x-inputs.input
                            name="specialization"
                            label="{{ __('Specialization') }}"
                            placeholder="{{ __('Enter specialization') }}"
                            :value="old('specialization')"
                        />

                        <x-inputs.input
                            name="phone"
                            label="{{ __('Phone Number') }}"
                            placeholder="{{ __('Enter phone number') }}"
                            :value="old('phone')"
                        />
                    </div>

                    <div class="space-y-6">
                        <x-inputs.input
                            name="email"
                            label="{{ __('Email Address') }}"
                            type="email"
                            placeholder="{{ __('Enter email address') }}"
                            :value="old('email')"
                        />

                        <x-inputs.select
                            name="hospital_id"
                            label="{{ __('Hospital') }}"
                            :options="$hospitals->pluck('name', 'id')->toArray()"
                            required
                            :value="old('hospital_id')"
                        />

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

                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.doctors.index') }}">
                        {{ __('Cancel') }}
                    </x-buttons.button>
                    <x-buttons.button variant="primary" type="submit">
                        <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Create Doctor') }}
                    </x-buttons.button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>

