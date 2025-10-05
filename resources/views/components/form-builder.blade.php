@props(['value' => null, 'name' => 'custom_fields_config'])

<div x-data="formBuilder(@js($value))" class="space-y-4">
    <!-- Hidden input for actual JSON data -->
    <input type="hidden" name="{{ $name }}" x-model="jsonOutput">

    <!-- Add Field Button -->
    <div class="flex items-center justify-between mb-4">
        <h4 class="form-label mb-0">{{ __('Form Fields') }}</h4>
        <button type="button" @click="addField()" class="btn-primary">
            <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
            {{ __('Add Field') }}
        </button>
    </div>

    <!-- Fields List -->
    <div class="space-y-4">
        <template x-for="(field, index) in fields" :key="field.id">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <!-- Field Header -->
                <div class="mb-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <div class="flex items-center space-x-3">
                        <button type="button" @click="moveUp(index)" x-show="index > 0" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                            <iconify-icon icon="lucide:chevron-up" class="h-5 w-5"></iconify-icon>
                        </button>
                        <button type="button" @click="moveDown(index)" x-show="index < fields.length - 1" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                            <iconify-icon icon="lucide:chevron-down" class="h-5 w-5"></iconify-icon>
                        </button>
                        <span class="form-label mb-0" x-text="'Field ' + (index + 1)"></span>
                        <span class="badge-primary" x-text="field.type"></span>
                    </div>
                    <button type="button" @click="removeField(index)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <iconify-icon icon="lucide:trash-2" class="h-5 w-5"></iconify-icon>
                    </button>
                </div>

                <!-- Field Configuration -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Field Key -->
                    <div>
                        <label class="form-label">{{ __('Field Key') }}</label>
                        <input type="text" x-model="field.key" @input="updateJson()" placeholder="field_name" class="form-control">
                        <div class="text-xs text-gray-400 mt-1">{{ __('Unique identifier (no spaces)') }}</div>
                    </div>

                    <!-- Field Label -->
                    <div>
                        <label class="form-label">{{ __('Field Label') }}</label>
                        <input type="text" x-model="field.label" @input="updateJson()" placeholder="Field Label" class="form-control">
                        <div class="text-xs text-gray-400 mt-1">{{ __('Display name for users') }}</div>
                    </div>

                    <!-- Field Type -->
                    <div>
                        <label class="form-label">{{ __('Field Type') }}</label>
                        <select x-model="field.type" @change="updateJson()" class="form-control">
                            <option value="text">{{ __('Text') }}</option>
                            <option value="textarea">{{ __('Textarea') }}</option>
                            <option value="email">{{ __('Email') }}</option>
                            <option value="url">{{ __('URL') }}</option>
                            <option value="number">{{ __('Number') }}</option>
                            <option value="date">{{ __('Date') }}</option>
                            <option value="datetime">{{ __('Date & Time') }}</option>
                            <option value="select">{{ __('Dropdown') }}</option>
                            <option value="checkbox">{{ __('Checkbox') }}</option>
                        </select>
                    </div>

                    <!-- Required -->
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" x-model="field.required" @change="updateJson()" class="form-checkbox">
                        <label class="form-label mb-0">{{ __('Required Field') }}</label>
                    </div>
                </div>

                <!-- Options for Select Type -->
                <div x-show="field.type === 'select'" class="mt-4">
                    <label class="form-label">{{ __('Options') }}</label>
                    <div class="space-y-2">
                        <template x-for="(option, optIndex) in field.options" :key="optIndex">
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="option.value" @input="updateJson()" placeholder="value" class="form-control w-1/3">
                                <input type="text" x-model="option.label" @input="updateJson()" placeholder="Label" class="form-control flex-1">
                                <button type="button" @click="removeOption(index, optIndex)" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                    <iconify-icon icon="lucide:x" class="h-5 w-5"></iconify-icon>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addOption(index)" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center gap-1">
                            <iconify-icon icon="lucide:plus" class="h-4 w-4"></iconify-icon>
                            {{ __('Add Option') }}
                        </button>
                    </div>
                </div>

                <!-- Data Source for Select Type -->
                <div x-show="field.type === 'select'" class="mt-4">
                    <label class="form-label">{{ __('Or Use Data Source') }}</label>
                    <select x-model="field.data_source" @change="updateJson()" class="form-control">
                        <option value="">{{ __('-- Manual Options --') }}</option>
                        <option value="hospitals">{{ __('Hospitals') }}</option>
                        <option value="doctors">{{ __('Doctors') }}</option>
                    </select>
                    <div class="text-xs text-gray-400 mt-1">{{ __('Load options from database') }}</div>
                </div>

                <!-- Validation for Date/DateTime -->
                <div x-show="field.type === 'date' || field.type === 'datetime'" class="mt-4">
                    <label class="form-label">{{ __('Validation') }}</label>
                    <select x-model="field.validation" @change="updateJson()" class="form-control">
                        <option value="">{{ __('None') }}</option>
                        <option value="future">{{ __('Future Date Only') }}</option>
                        <option value="past">{{ __('Past Date Only') }}</option>
                    </select>
                </div>

                <!-- Conditional Logic -->
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">{{ __('Conditional Logic') }}</label>
                        <button type="button" @click="field.show_if = field.show_if ? null : {field: '', value: ''}; updateJson()" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            <span x-show="!field.show_if">{{ __('Add Condition') }}</span>
                            <span x-show="field.show_if">{{ __('Remove Condition') }}</span>
                        </button>
                    </div>
                    <div x-show="field.show_if" class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="text" x-model="field.show_if.field" @input="updateJson()" placeholder="field_key" class="form-control">
                            <div class="text-xs text-gray-400 mt-1">{{ __('Show when field') }}</div>
                        </div>
                        <div>
                            <input type="text" x-model="field.show_if.value" @input="updateJson()" placeholder="value" class="form-control">
                            <div class="text-xs text-gray-400 mt-1">{{ __('Equals value') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="fields.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
            <iconify-icon icon="lucide:layout-list" class="mx-auto h-12 w-12 text-gray-400"></iconify-icon>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No fields added') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Click "Add Field" to get started') }}</p>
        </div>
    </div>

    <!-- Quick Templates -->
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
        <h4 class="mb-3 form-label">{{ __('Quick Templates') }}</h4>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="loadTemplate('appointment')" class="btn-success">
                {{ __('Appointment Service') }}
            </button>
            <button type="button" @click="loadTemplate('lab_test')" class="rounded-md bg-purple-600 px-3 py-2 text-sm font-medium text-white hover:bg-purple-700">
                {{ __('Lab Test') }}
            </button>
            <button type="button" @click="loadTemplate('simple')" class="btn-primary">
                {{ __('Simple Notes') }}
            </button>
        </div>
    </div>
</div>

<script>
function formBuilder(initialValue) {
    return {
        fields: [],
        jsonOutput: '',

        init() {
            // Load initial value if provided
            if (initialValue) {
                try {
                    // Handle both string and object inputs
                    let parsed;
                    if (typeof initialValue === 'string') {
                        parsed = JSON.parse(initialValue);
                    } else if (typeof initialValue === 'object' && initialValue !== null) {
                        parsed = initialValue;
                    }

                    if (parsed && parsed.fields) {
                        this.fields = parsed.fields.map((field, index) => ({
                            id: Date.now() + index,
                            ...field,
                            options: field.options || [],
                            show_if: field.show_if || null
                        }));
                    }
                } catch (e) {
                    console.error('Failed to parse initial value:', e);
                }
            }
            this.updateJson();
        },
        
        addField() {
            this.fields.push({
                id: Date.now(),
                key: '',
                label: '',
                type: 'text',
                required: false,
                options: [],
                show_if: null
            });
            this.updateJson();
        },
        
        removeField(index) {
            if (confirm('{{ __("Are you sure you want to remove this field?") }}')) {
                this.fields.splice(index, 1);
                this.updateJson();
            }
        },
        
        moveUp(index) {
            if (index > 0) {
                [this.fields[index], this.fields[index - 1]] = [this.fields[index - 1], this.fields[index]];
                this.updateJson();
            }
        },
        
        moveDown(index) {
            if (index < this.fields.length - 1) {
                [this.fields[index], this.fields[index + 1]] = [this.fields[index + 1], this.fields[index]];
                this.updateJson();
            }
        },
        
        addOption(fieldIndex) {
            this.fields[fieldIndex].options.push({ value: '', label: '' });
            this.updateJson();
        },
        
        removeOption(fieldIndex, optionIndex) {
            this.fields[fieldIndex].options.splice(optionIndex, 1);
            this.updateJson();
        },
        
        updateJson() {
            const cleanFields = this.fields.map(field => {
                const clean = {
                    key: field.key,
                    label: field.label,
                    type: field.type,
                    required: field.required
                };
                
                if (field.type === 'select') {
                    if (field.data_source) {
                        clean.data_source = field.data_source;
                    } else if (field.options && field.options.length > 0) {
                        clean.options = field.options.filter(opt => opt.value && opt.label);
                    }
                }
                
                if (field.validation) {
                    clean.validation = field.validation;
                }
                
                if (field.show_if && field.show_if.field && field.show_if.value) {
                    clean.show_if = field.show_if;
                }
                
                return clean;
            });
            
            this.jsonOutput = JSON.stringify({ fields: cleanFields }, null, 2);
        },
        
        loadTemplate(type) {
            const templates = {
                appointment: [
                    { key: 'appointment_type', label: 'Appointment Type', type: 'select', required: true, options: [{value: 'self', label: 'Self'}, {value: 'someone_else', label: 'Someone Else'}] },
                    { key: 'patient_name', label: 'Patient Name', type: 'text', required: true, show_if: {field: 'appointment_type', value: 'someone_else'} },
                    { key: 'hospital_id', label: 'Select Hospital', type: 'select', required: true, data_source: 'hospitals' },
                    { key: 'doctor_id', label: 'Select Doctor', type: 'select', required: true, data_source: 'doctors' },
                    { key: 'appointment_time', label: 'Appointment Date & Time', type: 'datetime', required: true, validation: 'future' }
                ],
                lab_test: [
                    { key: 'test_type', label: 'Test Type', type: 'select', required: true, options: [{value: 'blood', label: 'Blood Test'}, {value: 'urine', label: 'Urine Test'}, {value: 'xray', label: 'X-Ray'}] },
                    { key: 'fasting_required', label: 'Fasting Required', type: 'checkbox', required: false },
                    { key: 'preferred_date', label: 'Preferred Date', type: 'date', required: true, validation: 'future' }
                ],
                simple: [
                    { key: 'notes', label: 'Additional Notes', type: 'textarea', required: false }
                ]
            };
            
            this.fields = (templates[type] || []).map((field, index) => ({
                id: Date.now() + index,
                ...field,
                options: field.options || [],
                show_if: field.show_if || null
            }));
            this.updateJson();
        }
    };
}
</script>

