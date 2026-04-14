<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Provider;
use Illuminate\Support\Facades\DB;

class ProviderImport
{
    public function rules(): array
    {
        return [
            'Name' => 'required|string|max:255|unique:providers,name',
            'Email' => 'required|email|unique:providers,email',
            'Phone' => 'required|string|max:20',
            'Status' => 'required|in:active,inactive',
        ];
    }

    public function requiredHeaders(): array
    {
        return ['Name', 'Email', 'Phone', 'Status'];
    }

    public function processRow(array $row, int $rowNumber): void
    {
        DB::transaction(function () use ($row) {
            Provider::create([
                'name' => $row['Name'],
                'email' => $row['Email'],
                'phone' => $row['Phone'],
                'status' => $row['Status'],
            ]);
        });
    }

    public function instructions(): array
    {
        return [
            'Download the sample template below',
            'Fill in your provider data following the format',
            'Ensure all required fields are completed',
            'Name and Email must be unique',
            'Status must be either "active" or "inactive"',
            'Upload the completed CSV file',
        ];
    }
}
