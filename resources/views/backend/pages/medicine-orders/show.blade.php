<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Order Header -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Created on') }} {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if ($order->payment_status === 'completed')
                            <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-semibold bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                                {{ __('Paid') }}
                            </span>
                        @endif
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800',
                                'in_office' => 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                                'delivered' => 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
                                'cancelled' => 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-semibold {{ $statusClasses[$order->status] ?? $statusClasses['pending'] }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="p-6">
                <div class="flex flex-wrap gap-3">
                    @if ($order->status === 'pending')
                        <form action="{{ route('admin.medicine-orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_office">
                            <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                <iconify-icon icon="lucide:building-2" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Mark In Office') }}
                            </button>
                        </form>
                    @endif

                    @if ($order->status === 'in_office')
                        <form action="{{ route('admin.medicine-orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                <iconify-icon icon="lucide:truck" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Mark Delivered') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Medicine Items -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Medicine Items') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Medicine') }}</th>
                                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Qty') }}</th>
                                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Cost') }}</th>
                                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Profit') }}</th>
                                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Unit Price') }}</th>
                                        <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td class="px-3 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->medicine_name }}</td>
                                            <td class="px-3 py-4 text-right text-sm text-gray-500">{{ $item->quantity }}</td>
                                            <td class="px-3 py-4 text-right text-sm text-gray-500">${{ number_format($item->cost, 2) }}</td>
                                            <td class="px-3 py-4 text-right text-sm text-gray-500">${{ number_format($item->profit, 2) }}</td>
                                            <td class="px-3 py-4 text-right text-sm text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-3 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">${{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                @if ($order->requires_delivery)
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Delivery Information') }}</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Pick-up Location') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->pickupLocation->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Drop-off Location') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->dropoffLocation->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Delivery Price') }}</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($order->delivery_price, 2) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Order Summary') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Subtotal') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</dd>
                            </div>
                            @if ($order->requires_delivery && $order->delivery_price > 0)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">{{ __('Delivery') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->delivery_price, 2) }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
                                <dt class="text-base font-medium text-gray-900 dark:text-white">{{ __('Total') }}</dt>
                                <dd class="text-base font-bold text-primary-600">${{ number_format($order->total, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Customer') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->customer->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->customer->phone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Supplier') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->phone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Payment') }}</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ __('Method') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</dd>
                            </div>
                            @if ($order->payment_reference)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Reference') }}</dt>
                                    <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $order->payment_reference }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
