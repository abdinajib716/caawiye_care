<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Service Details') }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Step 2 of 4') }}
        </span>
    </div>

    @if($hasCustomFieldServices)
        <div class="space-y-6">
            @foreach($services as $service)
                @php
                    $serviceModel = \App\Models\Service::find($service['id']);
                @endphp

                @if($serviceModel && $serviceModel->hasCustomFields())
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <!-- Service Name Header -->
                        <div class="mb-4 flex items-center space-x-2 border-b border-gray-200 pb-3 dark:border-gray-700">
                            <iconify-icon icon="lucide:clipboard-list" class="h-5 w-5 text-blue-600 dark:text-blue-400"></iconify-icon>
                            <h4 class="font-medium text-gray-900 dark:text-white">
                                {{ $serviceModel->name }}
                            </h4>
                        </div>

                        <!-- Dynamic Fields -->
                        <div class="space-y-4">
                            @foreach($serviceModel->getCustomFields() as $field)
                                @php
                                    $fieldKey = $service['id'] . '_' . $field['key'];
                                    $shouldShow = $this->shouldShowField($service['id'], $field);
                                @endphp

                                @if($shouldShow)
                                    <div>
                                        <label for="{{ $fieldKey }}" class="form-label">
                                            {{ $field['label'] }}
                                            @if(!empty($field['required']))
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        @if($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'url')
                                            <input
                                                type="{{ $field['type'] }}"
                                                id="{{ $fieldKey }}"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                class="form-control @if(isset($validationErrors[$fieldKey])) border-red-500 @endif"
                                                placeholder="{{ $field['label'] }}"
                                            />

                                        @elseif($field['type'] === 'number')
                                            <input
                                                type="number"
                                                id="{{ $fieldKey }}"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                class="form-control @if(isset($validationErrors[$fieldKey])) border-red-500 @endif"
                                                placeholder="{{ $field['label'] }}"
                                            />

                                        @elseif($field['type'] === 'textarea')
                                            <textarea
                                                id="{{ $fieldKey }}"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                rows="3"
                                                class="form-control @if(isset($validationErrors[$fieldKey])) border-red-500 @endif"
                                                placeholder="{{ $field['label'] }}"
                                            ></textarea>

                                        @elseif($field['type'] === 'select')
                                            <select
                                                id="{{ $fieldKey }}"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                class="form-control @if(isset($validationErrors[$fieldKey])) border-red-500 @endif"
                                            >
                                                <option value="">{{ __('Select') }} {{ $field['label'] }}</option>

                                                @if(!empty($field['data_source']) && $field['data_source'] === 'hospitals')
                                                    @foreach($hospitals as $hospital)
                                                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                                                    @endforeach

                                                @elseif(!empty($field['data_source']) && $field['data_source'] === 'doctors')
                                                    @php
                                                        $filteredDoctors = $this->getFilteredDoctors($service['id']);
                                                        $selectedHospitalId = $this->getSelectedHospitalId($service['id']);
                                                    @endphp

                                                    @if($filteredDoctors->isEmpty())
                                                        @if($selectedHospitalId)
                                                            <option value="" disabled>{{ __('No doctors available for this hospital') }}</option>
                                                        @else
                                                            <option value="" disabled>{{ __('Please select a hospital first') }}</option>
                                                        @endif
                                                    @else
                                                        @foreach($filteredDoctors as $doctor)
                                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                        @endforeach
                                                    @endif

                                                @elseif(!empty($field['options']))
                                                    @foreach($field['options'] as $option)
                                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                            @if(!empty($field['data_source']) && $field['data_source'] === 'doctors')
                                                @php
                                                    $filteredDoctors = $this->getFilteredDoctors($service['id']);
                                                    $selectedHospitalId = $this->getSelectedHospitalId($service['id']);
                                                @endphp
                                                @if($filteredDoctors->isEmpty())
                                                    @if($selectedHospitalId)
                                                        <div class="mt-1 text-xs text-yellow-600">
                                                            <iconify-icon icon="lucide:alert-triangle" class="inline h-3 w-3"></iconify-icon>
                                                            {{ __('No doctors available for the selected hospital') }}
                                                        </div>
                                                    @else
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            <iconify-icon icon="lucide:info" class="inline h-3 w-3"></iconify-icon>
                                                            {{ __('Select a hospital to see available doctors') }}
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif

                                        @elseif($field['type'] === 'date')
                                            <x-inputs.date-picker
                                                :id="$fieldKey"
                                                :name="'fieldData.' . $fieldKey"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                :label="null"
                                                :class="isset($validationErrors[$fieldKey]) ? 'border-red-500' : ''"
                                            />

                                        @elseif($field['type'] === 'datetime')
                                            <input
                                                type="datetime-local"
                                                id="{{ $fieldKey }}"
                                                wire:model.live="fieldData.{{ $fieldKey }}"
                                                class="form-control {{ isset($validationErrors[$fieldKey]) ? 'border-red-500' : '' }}"
                                            />

                                        @elseif($field['type'] === 'checkbox')
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    id="{{ $fieldKey }}"
                                                    wire:model.live="fieldData.{{ $fieldKey }}"
                                                    class="form-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                                />
                                                <label for="{{ $fieldKey }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $field['label'] }}
                                                </label>
                                            </div>
                                        @endif

                                        @if(isset($validationErrors[$fieldKey]))
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ $validationErrors[$fieldKey] }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('go-to-step', { detail: { step: 1 } }))"
                class="btn-secondary"
            >
                <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('Back') }}
            </button>

            <button
                type="button"
                wire:click="validateAndProceed"
                class="btn-primary"
            >
                {{ __('Continue') }}
                <iconify-icon icon="lucide:arrow-right" class="ml-2 h-4 w-4"></iconify-icon>
            </button>
        </div>
    @else
        <!-- No custom fields - auto proceed -->
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800">
            <iconify-icon icon="lucide:check-circle" class="mx-auto h-12 w-12 text-green-500"></iconify-icon>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('No additional details required for selected services.') }}
            </p>
        </div>
    @endif
</div>

