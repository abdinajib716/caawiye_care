<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Edit Hospital') }}</h3>
            </x-slot>

            <form action="{{ route('admin.hospitals.update', $hospital) }}" method="POST" class="space-y-6" x-data="hospitalForm()">
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

                <!-- Doctors Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-base font-medium text-gray-900">{{ __('Doctors') }}</h4>
                        <button type="button" @click="addDoctor()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Add Doctor') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(doctor, index) in doctors" :key="index">
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-medium text-gray-700" x-text="'Doctor ' + (index + 1)"></h5>
                                    <button type="button" @click="removeDoctor(index)" class="text-red-600 hover:text-red-800">
                                        <iconify-icon icon="lucide:trash-2" class="h-4 w-4"></iconify-icon>
                                    </button>
                                </div>
                                <input type="hidden" :name="'doctors[' + index + '][id]'" x-model="doctor.id">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Doctor Name') }} *</label>
                                        <input type="text" :name="'doctors[' + index + '][name]'" x-model="doctor.name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter doctor name') }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Specialization') }}</label>
                                        <input type="text" :name="'doctors[' + index + '][specialization]'" x-model="doctor.specialization" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter specialization') }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone Number') }}</label>
                                        <input type="text" :name="'doctors[' + index + '][phone]'" x-model="doctor.phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter phone number') }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email Address') }}</label>
                                        <input type="email" :name="'doctors[' + index + '][email]'" x-model="doctor.email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter email address') }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }} *</label>
                                        <select :name="'doctors[' + index + '][status]'" x-model="doctor.status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="active">{{ __('Active') }}</option>
                                            <option value="inactive">{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="doctors.length === 0" class="text-center py-8 text-gray-500">
                            {{ __('No doctors added yet. Click "Add Doctor" to add doctors to this hospital.') }}
                        </div>
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

    @push('scripts')
    <script>
        function hospitalForm() {
            return {
                doctors: @json($hospital->doctors->map(function($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'specialization' => $doctor->specialization,
                        'phone' => $doctor->phone,
                        'email' => $doctor->email,
                        'status' => $doctor->status
                    ];
                })),
                addDoctor() {
                    this.doctors.push({
                        id: null,
                        name: '',
                        specialization: '',
                        phone: '',
                        email: '',
                        status: 'active'
                    });
                },
                removeDoctor(index) {
                    this.doctors.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-layouts.backend-layout>

