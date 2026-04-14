<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderExport
{
    public function headers(): array
    {
        return [
            'Order Number',
            'Customer',
            'Phone',
            'Total',
            'Payment Status',
            'Status',
            'Created At',
        ];
    }

    public function data(): Collection
    {
        return Order::query()
            ->with('customer')
            ->latest()
            ->get()
            ->map(function (Order $order) {
                return [
                    $order->order_number,
                    $order->customer?->name ?? '',
                    $order->customer?->phone ?? '',
                    number_format((float) $order->total, 2, '.', ''),
                    $order->payment_status,
                    $order->status,
                    $order->created_at?->format('Y-m-d H:i:s') ?? '',
                ];
            });
    }

    public function filename(): string
    {
        return 'orders_' . now()->format('Y-m-d_His');
    }
}
