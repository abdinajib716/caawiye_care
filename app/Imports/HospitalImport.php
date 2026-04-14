<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Hospital;
use Illuminate\Support\Facades\DB;

class HospitalImport
{
    public function rules(): array
    {
        return [
            'Name' => 'required|string|max:255|unique:hospitals,name',
            'Email' => 'required|email|unique:hospitals,email',
            'Phone' => 'required|string|max:20',
            'Address' => 'required|string',
            'Status' => 'required|in:active,inactive',
        ];
    }

    public function requiredHeaders(): array
    {
        return ['Name', 'Email', 'Phone', 'Address', 'Status'];
    }

    public function processRow(array $row, int $rowNumber): void
    {
        DB::transaction(function () use ($row) {
            Hospital::create([
                'name' => $row['Name'],
                'email' => $row['Email'],
                'phone' => $row['Phone'],
                'address' => $row['Address'],
                'status' => $row['Status'],
            ]);
        });
    }

    public function instructions(): array
    {
        return [
            'Download the sample template below',
            'Fill in your hospital data following the format',
            'Ensure all required fields are completed',
            'Name and Email must be unique',
            'Status must be either "active" or "inactive"',
            'Upload the completed CSV file',
        ];
    }
}
