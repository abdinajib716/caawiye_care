<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Order #:number', ['number' => $order->order_number]) }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Created on :date', ['date' => $order->created_at->format('M d, Y \a\t h:i A')]) }}
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Back to Orders') }}
                </a>
                <button
                    onclick="window.print()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                >
                    <iconify-icon icon="lucide:printer" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Print Receipt') }}
                </button>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex items-center space-x-4">
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Order Status:') }}</span>
                <span class="ml-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $order->status_color }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Payment Status:') }}</span>
                <span class="ml-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $order->payment_status_color }}">
                    {{ $order->payment_status_label }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Order Details -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Customer Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Customer Information') }}
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:user" class="mr-3 h-5 w-5 text-gray-400"></iconify-icon>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Name') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <iconify-icon icon="lucide:phone" class="mr-3 h-5 w-5 text-gray-400"></iconify-icon>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Phone') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer->phone }}</p>
                            </div>
                        </div>
                        @if($order->customer->address)
                            <div class="flex items-center">
                                <iconify-icon icon="lucide:map-pin" class="mr-3 h-5 w-5 text-gray-400"></iconify-icon>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Address') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer->address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Order Items') }}
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        {{ __('Service') }}
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        {{ __('Unit Price') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        {{ __('Total') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->service_name }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-center text-gray-900 dark:text-white">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-gray-900 dark:text-white">
                                            ${{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-4 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Subtotal') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format($order->subtotal, 2) }}
                                    </td>
                                </tr>
                                @if($order->tax > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Tax') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                            ${{ number_format($order->tax, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                @if($order->discount > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ __('Discount') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-red-600 dark:text-red-400">
                                            -${{ number_format($order->discount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td colspan="3" class="px-4 py-3 text-right text-base font-bold text-gray-900 dark:text-white">
                                        {{ __('Total') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xl font-bold text-blue-600 dark:text-blue-400">
                                        ${{ number_format($order->total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Appointment Information -->
                @php
                    $appointments = $order->items->flatMap(fn($item) => $item->appointment ? [$item->appointment] : []);
                @endphp
                @if($appointments->isNotEmpty())
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('Appointment Information') }}
                        </h2>
                        @foreach($appointments as $appointment)
                            <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700/50">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Hospital') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->hospital->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Appointment Time') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->formatted_appointment_time }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Status') }}</p>
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $appointment->status_color }}">
                                            {{ $appointment->status_label }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Appointment Type') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->appointment_type_label }}</p>
                                    </div>
                                    @if($appointment->patient_name)
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Patient Name') }}</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->patient_name }}</p>
                                        </div>
                                    @endif
                                </div>
                                @can('appointment.view')
                                    <div class="mt-4">
                                        <a href="{{ route('admin.appointments.show', $appointment) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <iconify-icon icon="lucide:calendar-check" class="mr-2 h-4 w-4"></iconify-icon>
                                            {{ __('View Appointment Details') }}
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Notes -->
                @if($order->notes)
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('Notes') }}
                        </h2>
                        <p class="text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Column: Payment & Agent Info -->
            <div class="space-y-6">
                <!-- Payment Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Payment Information') }}
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Payment Method') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        @if($order->payment_provider)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Provider') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $order->payment_provider }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Payment Phone') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->payment_phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Payment Status') }}</p>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $order->payment_status_color }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Agent Information -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Agent Information') }}
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Created By') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->agent->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Email') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $order->agent->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Timeline') }}
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <iconify-icon icon="lucide:plus" class="h-4 w-4 text-blue-600 dark:text-blue-400"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Order Created') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                        @if($order->completed_at)
                            <div class="flex items-start">
                                <div class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                    <iconify-icon icon="lucide:check" class="h-4 w-4 text-green-600 dark:text-green-400"></iconify-icon>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Order Completed') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->completed_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
    @endpush
</x-layouts.backend-layout>

