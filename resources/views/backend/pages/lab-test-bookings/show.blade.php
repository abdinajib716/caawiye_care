<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Booking Header -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $booking->booking_number }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Lab Test Booking') }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-{{ $booking->status_color }}-100 text-{{ $booking->status_color }}-800 border border-{{ $booking->status_color }}-200 dark:bg-{{ $booking->status_color }}-900/20 dark:text-{{ $booking->status_color }}-400 dark:border-{{ $booking->status_color }}-800">
                        {{ $booking->status_label }}
                    </span>
                </div>

                <!-- Status Actions -->
                @if($booking->status === 'pending')
                    <div class="flex items-center space-x-2 mt-4">
                        @can('lab_test_booking.edit')
                            <form method="POST" action="{{ route('admin.lab-test-bookings.confirm', $booking) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <iconify-icon icon="lucide:check-circle" class="w-4 h-4 mr-2"></iconify-icon>
                                    {{ __('Confirm Booking') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.lab-test-bookings.cancel', $booking) }}"
                                onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <iconify-icon icon="lucide:x-circle" class="w-4 h-4 mr-2"></iconify-icon>
                                    {{ __('Cancel Booking') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                @elseif($booking->status === 'confirmed')
                    <div class="flex items-center space-x-2 mt-4">
                        @can('lab_test_booking.edit')
                            <form method="POST" action="{{ route('admin.lab-test-bookings.mark-in-progress', $booking) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                    <iconify-icon icon="lucide:loader" class="w-4 h-4 mr-2"></iconify-icon>
                                    {{ __('Mark In Progress') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                @elseif($booking->status === 'in_progress')
                    <div class="flex items-center space-x-2 mt-4">
                        @can('lab_test_booking.edit')
                            <form method="POST" action="{{ route('admin.lab-test-bookings.complete', $booking) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <iconify-icon icon="lucide:check-check" class="w-4 h-4 mr-2"></iconify-icon>
                                    {{ __('Mark Completed') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Booking Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Customer & Patient Information -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                        {{ __('Customer & Patient Information') }}
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-white">
                                <a href="{{ route('admin.customers.show', $booking->customer) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ $booking->customer->full_name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Patient Name') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $booking->patient_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Patient Address') }}</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $booking->patient_address ?? '-' }}</p>
                        </div>

                        @if($booking->assignedNurse)
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Assigned Nurse') }}</label>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ $booking->assignedNurse->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>

            <!-- Pricing Summary -->
            <x-card class="bg-white dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                        {{ __('Pricing Summary') }}
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Cost') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $booking->total_cost_formatted }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Commission') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $booking->commission_amount_formatted }}</span>
                        </div>

                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Total Amount') }}</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $booking->total_amount_formatted }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Profit') }}</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ $booking->profit_formatted }}</span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Payment Status') }}</span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                    {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' }}">
                                    {{ $booking->payment_status_label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Provider Payment') }}</span>
                                @php
                                    $providerPaymentStatus = $booking->provider_payment_status ?? 'unpaid';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                    @if($providerPaymentStatus === 'paid') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                    @elseif($providerPaymentStatus === 'reversed') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                    @endif">
                                    {{ ucfirst($providerPaymentStatus) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Lab Test Items -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                    {{ __('Lab Tests') }}
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Test Name') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Provider') }}
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Cost') }}
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Commission') }}
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('Subtotal') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($booking->items as $item)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('admin.lab-tests.show', $item->labTest) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            {{ $item->test_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $item->labTest->provider->name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                        {{ $item->cost_formatted }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                        {{ $item->commission_amount_formatted }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                                        {{ $item->subtotal_formatted }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>

        <!-- Notes -->
        @if($booking->notes)
            <x-card class="bg-white dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                        {{ __('Notes') }}
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $booking->notes }}</p>
                </div>
            </x-card>
        @endif

        <!-- System Information -->
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                    {{ __('System Information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <label class="text-gray-500 dark:text-gray-400">{{ __('Created At') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="text-gray-500 dark:text-gray-400">{{ __('Updated At') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $booking->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($booking->completed_at)
                        <div>
                            <label class="text-gray-500 dark:text-gray-400">{{ __('Completed At') }}</label>
                            <p class="text-gray-900 dark:text-white">{{ $booking->completed_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.lab-test-bookings.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <iconify-icon icon="lucide:arrow-left" class="w-4 h-4 mr-2"></iconify-icon>
                {{ __('Back to Bookings') }}
            </a>

        </div>
    </div>
</x-layouts.backend-layout>
