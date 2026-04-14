<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ScanImagingService;
use App\Models\Provider;
use Illuminate\Support\Facades\DB;

class ScanImagingServiceImport
{
    public function rules(): array
    {
        return [
            'Service Name' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Cost' => 'required|numeric|min:0',
            'Provider' => 'required|string',
            'Status' => 'required|in:active,inactive',
        ];
    }

    public function requiredHeaders(): array
    {
        return ['Service Name', 'Description', 'Cost', 'Provider', 'Status'];
    }

    public function processRow(array $row, int $rowNumber): void
    {
        DB::transaction(function () use ($row) {
            // Find provider by name
            $provider = Provider::where('name', $row['Provider'])->firstOrFail();

            ScanImagingService::create([
                'service_name' => $row['Service Name'],
                'description' => $row['Description'] ?: null,
                'cost' => (float) $row['Cost'],
                'provider_id' => $provider->id,
                'status' => $row['Status'],
            ]);
        });
    }

    public function instructions(): array
    {
        return [
            'Download the sample template below',
            'Fill in your scan/imaging service data following the format',
            'Ensure all required fields are completed',
            'Provider name must match an existing provider',
            'Cost must be a valid number (e.g., 50.00)',
            'Status must be either "active" or "inactive"',
            'Upload the completed CSV file',
        ];
    }
}
