<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\ScanImagingService;
use Illuminate\Support\Collection;

class ScanImagingServiceExport
{
    public function headers(): array
    {
        return [
            'ID',
            'Service Name',
            'Description',
            'Cost',
            'Provider',
            'Status',
            'Created At',
        ];
    }

    public function data(): Collection
    {
        return ScanImagingService::with('provider')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($service) {
                return [
                    $service->id,
                    $service->service_name,
                    $service->description ?? '',
                    number_format($service->cost, 2, '.', ''),
                    $service->provider->name ?? '',
                    $service->status,
                    $service->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function sampleData(): array
    {
        return [
            '1',
            'X-Ray Chest',
            'Chest X-Ray Examination',
            '50.00',
            'Imaging Center',
            'active',
            '2025-01-15 10:00:00',
        ];
    }

    public function filename(): string
    {
        return 'scan_imaging_services_' . now()->format('Y-m-d_His');
    }
}
