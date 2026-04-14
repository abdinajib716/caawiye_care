<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Provider;
use Illuminate\Support\Collection;

class ProviderExport
{
    public function headers(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Status',
            'Created At',
        ];
    }

    public function data(): Collection
    {
        return Provider::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($provider) {
                return [
                    $provider->id,
                    $provider->name,
                    $provider->email ?? '',
                    $provider->phone ?? '',
                    $provider->status,
                    $provider->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function sampleData(): array
    {
        return [
            '1',
            'Lab Services Ltd',
            'lab@services.com',
            '617123456',
            'active',
            '2025-01-15 10:00:00',
        ];
    }

    public function filename(): string
    {
        return 'providers_' . now()->format('Y-m-d_His');
    }
}
