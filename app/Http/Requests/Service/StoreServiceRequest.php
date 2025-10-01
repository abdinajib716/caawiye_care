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
        ]);
    }
}
