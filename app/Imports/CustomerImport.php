<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerImport
{
    /**
     * Get validation rules
     */
    public function rules(): array
    {
        return [
            'Name' => 'required|string|max:255',
            'Email' => 'nullable|email|unique:customers,email',
            'Phone' => 'required|string|max:20',
            'Country Code' => 'required|string|max:5',
            'Address' => 'nullable|string',
            'Status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get required headers
     */
    public function requiredHeaders(): array
    {
        return ['Name', 'Email', 'Phone', 'Country Code', 'Address', 'Status'];
    }

    /**
     * Process import row
     */
    public function processRow(array $row, int $rowNumber): void
    {
        DB::transaction(function () use ($row) {
            Customer::create([
                'name' => $row['Name'],
                'email' => $row['Email'] ?: null,
                'phone' => $row['Phone'],
                'country_code' => $row['Country Code'],
                'address' => $row['Address'] ?: null,
                'status' => $row['Status'],
                'password' => Hash::make('password'), // Default password
            ]);
        });
    }

    /**
     * Get import instructions
     */
    public function instructions(): array
    {
        return [
            'Download the sample template below',
            'Fill in your customer data following the format',
            'Ensure all required fields are completed',
            'Email must be unique (or leave empty)',
            'Status must be either "active" or "inactive"',
            'Upload the completed CSV file',
        ];
    }
}
