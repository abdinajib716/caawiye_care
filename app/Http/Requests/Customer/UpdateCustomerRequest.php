<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('customer'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'country_code' => ['required', 'string', 'max:5'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
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
            'name' => 'customer name',
            'phone' => 'phone number',
            'country_code' => 'country code',
            'address' => 'address',
            'status' => 'status',
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
            'name.required' => 'The customer name is required.',
            'name.max' => 'The customer name cannot exceed 255 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'country_code.required' => 'The country code is required.',
            'country_code.max' => 'The country code cannot exceed 5 characters.',
            'address.max' => 'The address cannot exceed 500 characters.',
            'status.required' => 'The customer status is required.',
            'status.in' => 'The customer status must be active or inactive.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'address' => $this->address ?: null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $customerId = $this->route('customer')->id;
            
            // Check for unique phone number within the same country (excluding current customer)
            $existingCustomer = Customer::where('phone', $this->phone)
                ->where('country_code', $this->country_code)
                ->where('id', '!=', $customerId)
                ->first();

            if ($existingCustomer) {
                $validator->errors()->add('phone', 'A customer with this phone number already exists in the selected country.');
            }
        });
    }
}
