<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Service;
use App\Models\ServiceFieldData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ServiceFieldDataService
{
    /**
     * Save field data for an order item.
     */
    public function saveFieldData(OrderItem $orderItem, Service $service, array $fieldData): void
    {
        DB::transaction(function () use ($orderItem, $service, $fieldData) {
            // Delete existing field data for this order item
            ServiceFieldData::where('order_item_id', $orderItem->id)->delete();

            // Save new field data
            foreach ($fieldData as $key => $value) {
                ServiceFieldData::create([
                    'order_item_id' => $orderItem->id,
                    'service_id' => $service->id,
                    'field_key' => $key,
                    'field_value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        });
    }

    /**
     * Get field data for an order item.
     */
    public function getFieldDataForOrderItem(OrderItem $orderItem): array
    {
        $fieldData = ServiceFieldData::where('order_item_id', $orderItem->id)->get();

        $result = [];
        foreach ($fieldData as $data) {
            $value = $data->field_value;
            
            // Try to decode JSON values
            $decoded = json_decode($value, true);
            $result[$data->field_key] = $decoded !== null ? $decoded : $value;
        }

        return $result;
    }

    /**
     * Get field data as key-value pairs.
     */
    public function getFieldDataAsKeyValue(OrderItem $orderItem): array
    {
        return $this->getFieldDataForOrderItem($orderItem);
    }

    /**
     * Validate field data against service configuration.
     *
     * @throws ValidationException
     */
    public function validateFieldData(Service $service, array $fieldData): array
    {
        if (!$service->hasCustomFields()) {
            return [];
        }

        $fields = $service->getCustomFields();
        $rules = [];
        $messages = [];
        $attributes = [];

        foreach ($fields as $field) {
            $fieldKey = $field['key'];
            $fieldRules = [];

            // Check if field is required
            if (!empty($field['required'])) {
                // Check conditional display
                if (!empty($field['show_if'])) {
                    $showIfField = $field['show_if']['field'];
                    $showIfValue = $field['show_if']['value'];
                    
                    // Only require if condition is met
                    if (isset($fieldData[$showIfField]) && $fieldData[$showIfField] == $showIfValue) {
                        $fieldRules[] = 'required';
                    }
                } else {
                    $fieldRules[] = 'required';
                }
            }

            // Add type-specific validation
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'date':
                case 'datetime':
                    $fieldRules[] = 'date';
                    
                    // Check for future date validation
                    if (!empty($field['validation']) && $field['validation'] === 'future') {
                        $fieldRules[] = 'after:now';
                        $messages["{$fieldKey}.after"] = "The {$field['label']} must be a future date and time.";
                    }
                    break;
                case 'select':
                    // Validate against options if provided
                    if (!empty($field['options']) && empty($field['data_source'])) {
                        $validValues = array_column($field['options'], 'value');
                        $fieldRules[] = 'in:' . implode(',', $validValues);
                    }
                    break;
            }

            if (!empty($fieldRules)) {
                $rules[$fieldKey] = $fieldRules;
            }

            // Set custom attribute name for better error messages
            $attributes[$fieldKey] = $field['label'] ?? $fieldKey;
        }

        // Perform validation
        $validator = Validator::make($fieldData, $rules, $messages, $attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get formatted field data for display.
     */
    public function getFormattedFieldData(OrderItem $orderItem, Service $service): array
    {
        $fieldData = $this->getFieldDataForOrderItem($orderItem);
        $fields = $service->getCustomFields();
        $formatted = [];

        foreach ($fields as $field) {
            $key = $field['key'];
            $value = $fieldData[$key] ?? null;

            if ($value === null) {
                continue;
            }

            $label = $field['label'] ?? $key;
            $formattedValue = $this->formatFieldValue($field, $value);

            $formatted[] = [
                'label' => $label,
                'value' => $formattedValue,
                'key' => $key,
                'type' => $field['type'] ?? 'text',
            ];
        }

        return $formatted;
    }

    /**
     * Format field value based on field type.
     */
    private function formatFieldValue(array $field, $value): string
    {
        switch ($field['type']) {
            case 'date':
                return date('M d, Y', strtotime($value));
            case 'datetime':
                return date('M d, Y h:i A', strtotime($value));
            case 'select':
                // Find label from options
                if (!empty($field['options'])) {
                    foreach ($field['options'] as $option) {
                        if ($option['value'] == $value) {
                            return $option['label'];
                        }
                    }
                }
                return $value;
            case 'checkbox':
                return $value ? 'Yes' : 'No';
            default:
                return (string) $value;
        }
    }

    /**
     * Delete field data for an order item.
     */
    public function deleteFieldData(OrderItem $orderItem): bool
    {
        return ServiceFieldData::where('order_item_id', $orderItem->id)->delete() > 0;
    }
}

