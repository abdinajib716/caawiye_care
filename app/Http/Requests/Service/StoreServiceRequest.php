<?php

declare(strict_types=1);

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('service.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:services,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'category_id' => ['nullable', 'exists:service_categories,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'discontinued'])],
            'service_type' => ['required', 'string', Rule::in(['standard', 'appointment'])],
            'has_custom_fields' => ['nullable', 'boolean'],
            'custom_fields_config' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // If custom fields are enabled, config must be provided
                    if ($this->has_custom_fields && empty($value)) {
                        $fail('Custom fields configuration is required when custom fields are enabled.');
                    }

                    // If config is provided, validate structure
                    if (!empty($value)) {
                        // Handle both string (JSON) and array inputs
                        $decoded = is_string($value) ? json_decode($value, true) : $value;

                        // Check if JSON decode failed
                        if (is_string($value) && json_last_error() !== JSON_ERROR_NONE) {
                            $fail('The custom fields configuration must be valid JSON.');
                            return;
                        }

                        // Check if it has 'fields' array
                        if (!isset($decoded['fields']) || !is_array($decoded['fields'])) {
                            $fail('Custom fields configuration must contain a "fields" array.');
                        }

                        // Check if fields array is not empty
                        if (isset($decoded['fields']) && empty($decoded['fields'])) {
                            $fail('Custom fields configuration must contain at least one field.');
                        }

                        // Validate each field has required properties
                        if (isset($decoded['fields']) && is_array($decoded['fields'])) {
                            foreach ($decoded['fields'] as $index => $field) {
                                if (!isset($field['key'])) {
                                    $fail("Field at index {$index} is missing required 'key' property.");
                                }
                                if (!isset($field['label'])) {
                                    $fail("Field at index {$index} is missing required 'label' property.");
                                }
                                if (!isset($field['type'])) {
                                    $fail("Field at index {$index} is missing required 'type' property.");
                                }
                            }
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'service name',
            'price' => 'service price',
            'cost' => 'service cost',
            'category_id' => 'service category',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The service name is required.',
            'name.unique' => 'A service with this name already exists.',
            'price.required' => 'The service price is required.',
            'price.numeric' => 'The service price must be a valid number.',
            'price.min' => 'The service price cannot be negative.',
            'cost.numeric' => 'The service cost must be a valid number.',
            'cost.min' => 'The service cost cannot be negative.',
            'category_id.exists' => 'The selected service category is invalid.',
            'status.required' => 'The service status is required.',
            'status.in' => 'The service status must be active, inactive, or discontinued.',
            'custom_fields_config.json' => 'The custom fields configuration must be valid JSON.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'short_description' => $this->short_description ?: null,
            'cost' => $this->cost ?: 0,
            'category_id' => $this->category_id ?: null,
            'service_type' => $this->service_type ?: 'standard',
            'has_custom_fields' => $this->has('has_custom_fields') ? (bool) $this->has_custom_fields : false,
            'custom_fields_config' => $this->custom_fields_config ? json_decode($this->custom_fields_config, true) : null,
        ]);
    }
}
