<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerExport
{
    /**
     * Get headers for export
     */
    public function headers(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Country Code',
            'Address',
            'Status',
            'Created At',
        ];
    }

    /**
     * Get data for export
     */
    public function data(): Collection
    {
        return Customer::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($customer) {
                return [
                    $customer->id,
                    $customer->name,
                    $customer->email ?? '',
                    $customer->phone,
                    $customer->country_code,
                    $customer->address ?? '',
                    $customer->status,
                    $customer->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Get sample data for template
     */
    public function sampleData(): array
    {
        return [
            '1',
            'John Doe',
            'john@example.com',
            '617123456',
            '+252',
            'Mogadishu, Somalia',
            'active',
            '2025-01-15 10:00:00',
        ];
    }

    /**
     * Get filename
     */
    public function filename(): string
    {
        return 'customers_' . now()->format('Y-m-d_His');
    }
}
