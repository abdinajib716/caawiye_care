<?php

declare(strict_types=1);

namespace App\Livewire\Datatable;

use App\Models\MedicineOrder;
use Illuminate\Database\Eloquent\Builder;

class MedicineOrderDatatable extends Datatable
{
    public string $model = MedicineOrder::class;

    public bool $showFilters = true;
    public array $relationships = ['customer', 'supplier', 'agent', 'items'];
    public array $searchableColumns = ['order_number'];
    public array $filterableColumns = [
        'status' => [
            'pending' => 'Pending',
            'in_office' => 'In Office',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ],
        'payment_status' => [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ],
    ];
    public array $sortableColumns = ['order_number', 'total', 'status', 'created_at'];

    public function query(): Builder
    {
        return MedicineOrder::query()
            ->with($this->relationships)
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $builder) {
                    $builder->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function (Builder $customerQuery) {
                            $customerQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('supplier', function (Builder $supplierQuery) {
                            $supplierQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filters['status'] ?? null, fn (Builder $query, $status) => $query->where('status', $status))
            ->when($this->filters['payment_status'] ?? null, fn (Builder $query, $status) => $query->where('payment_status', $status))
            ->orderBy($this->sort, $this->direction);
    }

    public function getRoutes(): array
    {
        return [
            'index' => 'admin.medicine-orders.index',
            'show' => 'admin.medicine-orders.show',
            'create' => 'admin.medicine-orders.create',
        ];
    }

    public function getPermissions(): array
    {
        return [
            'view' => 'medicine_order.view',
            'create' => 'medicine_order.create',
            'edit' => 'medicine_order.edit',
            'delete' => 'medicine_order.delete',
        ];
    }

    public function getModelNameSingular(): string
    {
        return __('Medicine Booking');
    }

    public function getModelNamePlural(): string
    {
        return __('Medicine Bookings');
    }

    public function getSearchbarPlaceholder(): string
    {
        return __('Search medicine bookings by order, customer, or supplier...');
    }

    public function getNewResourceLinkLabel(): string
    {
        return __('New Medicine Booking');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'order_number',
                'title' => __('Order #'),
                'sortable' => true,
                'sortBy' => 'order_number',
                'renderContent' => 'renderOrderNumberColumn',
            ],
            [
                'id' => 'customer',
                'title' => __('Customer'),
                'sortable' => false,
                'renderContent' => 'renderCustomerColumn',
            ],
            [
                'id' => 'supplier',
                'title' => __('Supplier'),
                'sortable' => false,
                'renderContent' => 'renderSupplierColumn',
            ],
            [
                'id' => 'items',
                'title' => __('Items'),
                'sortable' => false,
                'renderContent' => 'renderItemsColumn',
            ],
            [
                'id' => 'total',
                'title' => __('Total'),
                'sortable' => true,
                'sortBy' => 'total',
                'renderContent' => 'renderTotalColumn',
            ],
            [
                'id' => 'payment_status',
                'title' => __('Payment'),
                'sortable' => false,
                'renderContent' => 'renderPaymentStatusColumn',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'sortable' => true,
                'sortBy' => 'status',
                'renderContent' => 'renderStatusColumn',
            ],
            [
                'id' => 'date',
                'title' => __('Date'),
                'sortable' => true,
                'sortBy' => 'created_at',
                'renderContent' => 'renderDateColumn',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'is_action' => true,
            ],
        ];
    }

    public function renderOrderNumberColumn($order): string
    {
        return '<span class="font-mono font-semibold text-primary-600 dark:text-primary-400">' . e($order->order_number) . '</span>';
    }

    public function renderCustomerColumn($order): string
    {
        return '<div><div class="font-medium text-gray-900 dark:text-white">' . e($order->customer?->name ?? __('N/A')) . '</div><div class="text-xs text-gray-500 dark:text-gray-400">' . e($order->customer?->phone ?? '') . '</div></div>';
    }

    public function renderSupplierColumn($order): string
    {
        return '<span class="font-medium text-gray-900 dark:text-white">' . e($order->supplier?->name ?? __('N/A')) . '</span>';
    }

    public function renderItemsColumn($order): string
    {
        $count = $order->items->count();

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800">' .
            $count . ' ' . ($count === 1 ? __('item') : __('items')) .
            '</span>';
    }

    public function renderTotalColumn($order): string
    {
        return '<span class="font-semibold text-gray-900 dark:text-white">$' . number_format((float) $order->total, 2) . '</span>';
    }

    public function renderPaymentStatusColumn($order): string
    {
        $statuses = [
            'completed' => 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
            'pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
            'failed' => 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
        ];

        $colorClass = $statuses[$order->payment_status] ?? $statuses['pending'];

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ' . $colorClass . '">' .
            e(ucfirst($order->payment_status)) .
            '</span>';
    }

    public function renderStatusColumn($order): string
    {
        $statuses = [
            'pending' => ['color' => 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800', 'label' => __('Pending')],
            'in_office' => ['color' => 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800', 'label' => __('In Office')],
            'delivered' => ['color' => 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800', 'label' => __('Delivered')],
            'cancelled' => ['color' => 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800', 'label' => __('Cancelled')],
        ];

        $status = $statuses[$order->status] ?? $statuses['pending'];

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ' . $status['color'] . '">' .
            $status['label'] .
            '</span>';
    }

    public function renderDateColumn($order): string
    {
        return '<div class="text-sm text-gray-900 dark:text-white">' . $order->created_at->format('d M Y') . '</div>';
    }

    public function renderActionsColumn($order): string
    {
        $html = '<div class="flex items-center justify-end gap-2">';

        $html .= '<a href="' . route('admin.medicine-orders.show', $order) . '" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('View Booking') . '">';
        $html .= '<iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>';
        $html .= '</a>';

        if ($order->status === 'pending') {
            $html .= '<button type="button" wire:click="updateBookingStatus(' . $order->id . ', \'in_office\')" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="' . __('Mark In Office') . '">';
            $html .= '<iconify-icon icon="lucide:building-2" class="w-4 h-4"></iconify-icon>';
            $html .= '</button>';
        } elseif ($order->status === 'in_office') {
            $html .= '<button type="button" wire:click="updateBookingStatus(' . $order->id . ', \'delivered\')" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="' . __('Mark Delivered') . '">';
            $html .= '<iconify-icon icon="lucide:truck" class="w-4 h-4"></iconify-icon>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }

    public function updateBookingStatus(int $orderId, string $status): void
    {
        $order = MedicineOrder::findOrFail($orderId);

        $order->update(['status' => $status]);

        $this->dispatch('show-toast', [
            'message' => __('Medicine booking status updated successfully.'),
            'type' => 'success',
        ]);
    }
}
