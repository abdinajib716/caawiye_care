<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        {{-- Header with Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $collection->request_id }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Created') }} {{ $collection->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    ];
                @endphp
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusColors[$collection->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $collection->status_label }}
                </span>
                <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Customer & Patient Information --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:user" class="inline mr-2"></iconify-icon>
                        {{ __('Customer & Patient Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 border-b pb-2">{{ __('Customer') }}</h4>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Name') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Phone') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->customer_phone }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 border-b pb-2">{{ __('Patient') }}</h4>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Name') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->patient_name }}</p>
                            </div>
                            @if($collection->patient_reference)
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Reference / File Number') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $collection->patient_reference }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>

                {{-- Provider Information --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:building" class="inline mr-2"></iconify-icon>
                        {{ __('Provider Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Provider Type') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $collection->provider_type_label }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Provider Name') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $collection->provider_name }}</p>
                        </div>
                        @if($collection->provider_address)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">{{ __('Address') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->provider_address }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                @if($collection->medicineOrder)
                    <x-card class="bg-white dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <iconify-icon icon="lucide:pill" class="inline mr-2"></iconify-icon>
                            {{ __('Medicine Source') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Medicine Booking') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('admin.medicine-orders.show', $collection->medicineOrder) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $collection->medicineOrder->order_number }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Supplier') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->medicineOrder->supplier?->name ?? __('N/A') }}</p>
                            </div>
                            @if($collection->medicineOrder->requires_delivery)
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Pick-up') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $collection->medicineOrder->pickupLocation?->name ?? __('N/A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Drop-off') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $collection->medicineOrder->dropoffLocation?->name ?? __('N/A') }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>
                @endif

                {{-- Collection Details --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:clipboard-list" class="inline mr-2"></iconify-icon>
                        {{ __('Collection Details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Delivery Required') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                @if($collection->delivery_required)
                                    <span class="text-green-600">{{ __('Yes') }}</span>
                                @else
                                    <span class="text-gray-500">{{ __('No') }}</span>
                                @endif
                            </p>
                        </div>
                        @if($collection->delivery_required)
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Delivery Date & Time') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $collection->delivery_date?->format('M d, Y') }} 
                                    @if($collection->delivery_time)
                                        {{ \Carbon\Carbon::parse($collection->delivery_time)->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Pick-up') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->pickupLocation?->name ?? $collection->medicineOrder?->pickupLocation?->name ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Drop-off') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->dropoffLocation?->name ?? $collection->medicineOrder?->dropoffLocation?->name ?? __('N/A') }}</p>
                            </div>
                        @endif
                        @if($collection->internal_notes)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">{{ __('Internal Notes') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->internal_notes }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                {{-- Audit Log --}}
                @if($collection->logs->count() > 0)
                    <x-card class="bg-white dark:bg-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <iconify-icon icon="lucide:history" class="inline mr-2"></iconify-icon>
                            {{ __('Activity Log') }}
                        </h3>
                        <div class="space-y-4">
                            @foreach($collection->logs->sortByDesc('created_at') as $log)
                                <div class="flex items-start space-x-3 border-b border-gray-100 dark:border-gray-700 pb-3 last:border-0">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <iconify-icon icon="lucide:activity" class="h-4 w-4 text-blue-600 dark:text-blue-400"></iconify-icon>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            @if($log->old_value && $log->new_value)
                                                : {{ $log->old_value }} → {{ $log->new_value }}
                                            @endif
                                        </p>
                                        @if($log->notes)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log->notes }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                            {{ $log->performer ? $log->performer->first_name . ' ' . $log->performer->last_name : 'System' }}
                                            • {{ $log->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Payment Summary --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:credit-card" class="inline mr-2"></iconify-icon>
                        {{ __('Payment') }}
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Method') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $collection->payment_method_label }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Status') }}</span>
                            @php
                                $paymentColors = [
                                    'verified' => 'text-green-600 dark:text-green-400',
                                    'pending' => 'text-yellow-600 dark:text-yellow-400',
                                    'failed' => 'text-red-600 dark:text-red-400',
                                ];
                            @endphp
                            <span class="font-medium {{ $paymentColors[$collection->payment_status] ?? 'text-gray-600' }}">
                                {{ $collection->payment_status_label }}
                            </span>
                        </div>
                        @if($collection->payment_reference)
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('Reference') }}</span>
                                <span class="font-medium text-gray-900 dark:text-white text-xs">{{ $collection->payment_reference }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('Service Charge') }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($collection->service_charge, 2) }}</span>
                            </div>
                            @if($collection->delivery_required)
                                <div class="flex justify-between mt-2">
                                    <span class="text-gray-500">{{ __('Delivery Fee') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($collection->delivery_fee, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ __('Total') }}</span>
                                <span class="font-bold text-lg text-blue-600 dark:text-blue-400">${{ number_format($collection->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </x-card>

                {{-- Assignment --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:user-check" class="inline mr-2"></iconify-icon>
                        {{ __('Assignment') }}
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Assigned Staff') }}</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $collection->assignedStaff->first_name ?? '' }} {{ $collection->assignedStaff->last_name ?? '' }}
                            </p>
                        </div>
                        @if($collection->assignment_notes)
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Notes') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $collection->assignment_notes }}</p>
                            </div>
                        @endif
                        @if($collection->creator)
                            <div>
                                <p class="text-sm text-gray-500">{{ __('Created By') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $collection->creator->first_name }} {{ $collection->creator->last_name }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-card>

                {{-- Timestamps --}}
                <x-card class="bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <iconify-icon icon="lucide:clock" class="inline mr-2"></iconify-icon>
                        {{ __('Timeline') }}
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('Created') }}</span>
                            <span class="text-gray-900 dark:text-white">{{ $collection->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($collection->started_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('Started') }}</span>
                                <span class="text-gray-900 dark:text-white">{{ $collection->started_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                        @if($collection->completed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('Completed') }}</span>
                                <span class="text-gray-900 dark:text-white">{{ $collection->completed_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
