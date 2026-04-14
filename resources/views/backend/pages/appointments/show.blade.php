<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Appointment #:id', ['id' => $appointment->id]) }}</h1>
            <div class="flex space-x-3">
                <!-- Download PDF Button -->
                @can('appointment.view')
                    <a
                        href="{{ route('admin.appointments.export-booking-pdf', $appointment) }}"
                        target="_blank"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        <iconify-icon icon="lucide:file-text" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Download PDF') }}
                    </a>
                @endcan
                @if($appointment->isScheduled())
                    @can('appointment.edit')
                        <form action="{{ route('admin.appointments.confirm', $appointment) }}" method="POST" class="inline">
                            @csrf
                            <x-buttons.button variant="primary" type="submit">
                                <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Confirm') }}
                            </x-buttons.button>
                        </form>
                    @endcan
                @endif
                @if($appointment->isScheduled() || $appointment->isConfirmed())
                    @can('appointment.edit')
                        <button
                            type="button"
                            onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                            class="inline-flex items-center rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            <iconify-icon icon="lucide:x" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Cancel') }}
                        </button>
                    @endcan
                @endif
            </div>
        </div>

        <!-- Appointment Details -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Appointment Information') }}</h3>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Status') }}</p>
                    <p class="mt-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $appointment->status_color }}">
                            {{ $appointment->status_label }}
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Appointment Time') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $appointment->formatted_appointment_time }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Hospital') }}</p>
                    <p class="mt-1 text-base text-gray-900">
                        {{ $appointment->hospital?->name ?? __('Not assigned') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Customer') }}</p>
                    <p class="mt-1 text-base text-gray-900">
                        @if($appointment->customer)
                            <a href="{{ route('admin.customers.show', $appointment->customer) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $appointment->customer->name }}
                            </a>
                        @else
                            <span class="text-gray-400">{{ __('Deleted customer') }}</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Appointment Type') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $appointment->appointment_type_label }}</p>
                </div>

                @if($appointment->patient_name)
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Patient Name') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $appointment->patient_name }}</p>
                </div>
                @endif

                @if($appointment->notes)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">{{ __('Notes') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $appointment->notes }}</p>
                </div>
                @endif

                @if($appointment->cancellation_reason)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">{{ __('Cancellation Reason') }}</p>
                    <p class="mt-1 text-base text-red-600">{{ $appointment->cancellation_reason }}</p>
                </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Order') }}</p>
                    <p class="mt-1 text-base text-gray-900">
                        @if($appointment->order)
                            <a href="{{ route('admin.orders.show', $appointment->order) }}" class="text-blue-600 hover:text-blue-800">
                                {{ __('Order #:id', ['id' => $appointment->order_id]) }}
                            </a>
                        @else
                            <span class="text-gray-400">
                                {{ __('Order #:id (Deleted)', ['id' => $appointment->order_id]) }}
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Created At') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $appointment->created_at->format('M j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Cancel Modal -->
    <div id="cancel-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 py-6">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/75 dark:bg-gray-900/90 transition-opacity" aria-hidden="true" onclick="document.getElementById('cancel-modal').classList.add('hidden')"></div>
            
            <!-- Modal Content -->
            <div class="relative z-10 w-full max-w-lg transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-xl transition-all">
                <form action="{{ route('admin.appointments.cancel', $appointment) }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-6 py-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                                {{ __('Cancel Appointment') }}
                            </h3>
                            <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                            </button>
                        </div>
                        <div class="mt-4">
                            <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Cancellation Reason (Optional)') }}
                            </label>
                            <textarea
                                id="cancellation_reason"
                                name="cancellation_reason"
                                rows="4"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="{{ __('Enter reason for cancellation...') }}"
                            ></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex gap-3 justify-end">
                        <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            {{ __('Cancel Appointment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
