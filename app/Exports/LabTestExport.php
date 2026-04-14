<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\LabTest;
use Illuminate\Support\Collection;

class LabTestExport
{
    public function headers(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Cost',
            'Provider',
            'Status',
            'Created At',
        ];
    }

    public function data(): Collection
    {
        return LabTest::with('provider')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($labTest) {
                return [
                    $labTest->id,
                    $labTest->name,
                    $labTest->description ?? '',
                    number_format($labTest->cost, 2, '.', ''),
                    $labTest->provider->name ?? '',
                    $labTest->status,
                    $labTest->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function sampleData(): array
    {
        return [
            '1',
            'Blood Test',
            'Complete Blood Count',
            '25.00',
            'Lab Services Ltd',
            'active',
            '2025-01-15 10:00:00',
        ];
    }

    public function filename(): string
    {
        return 'lab_tests_' . now()->format('Y-m-d_His');
    }
}
