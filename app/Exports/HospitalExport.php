<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Hospital;
use Illuminate\Support\Collection;

class HospitalExport
{
    public function headers(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Status',
            'Created At',
        ];
    }

    public function data(): Collection
    {
        return Hospital::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($hospital) {
                return [
                    $hospital->id,
                    $hospital->name,
                    $hospital->email ?? '',
                    $hospital->phone,
                    $hospital->address ?? '',
                    $hospital->status,
                    $hospital->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function sampleData(): array
    {
        return [
            '1',
            'Central Hospital',
            'info@central.com',
            '617123456',
            'Main Street, Mogadishu',
            'active',
            '2025-01-15 10:00:00',
        ];
    }

    public function filename(): string
    {
        return 'hospitals_' . now()->format('Y-m-d_His');
    }
}
