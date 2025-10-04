<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search appointments...') }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            />
        </div>

        <div class="flex gap-2">
            <select
                wire:model.live="statusFilter"
                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
                <option value="">{{ __('All Status') }}</option>
                <option value="scheduled">{{ __('Scheduled') }}</option>
                <option value="confirmed">{{ __('Confirmed') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
                <option value="no_show">{{ __('No Show') }}</option>
            </select>

            <select
                wire:model.live="perPage"
                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="cursor-pointer px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400" wire:click="sortBy('id')">
                        {{ __('ID') }}
                        @if($sortField === 'id')
                            <iconify-icon icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="inline h-4 w-4"></iconify-icon>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Customer') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Hospital') }}
                    </th>
                    <th scope="col" class="cursor-pointer px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400" wire:click="sortBy('appointment_time')">
                        {{ __('Appointment Time') }}
                        @if($sortField === 'appointment_time')
                            <iconify-icon icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="inline h-4 w-4"></iconify-icon>
                        @endif
                    </th>
                    <th scope="col" class="cursor-pointer px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400" wire:click="sortBy('status')">
                        {{ __('Status') }}
                        @if($sortField === 'status')
                            <iconify-icon icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="inline h-4 w-4"></iconify-icon>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $appointment->id }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300">{{ $appointment->customer->name }}</div>
                            @if($appointment->patient_name)
                                <div class="text-xs text-gray-500">{{ __('Patient: :name', ['name' => $appointment->patient_name]) }}</div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300">{{ $appointment->hospital->name }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-300">{{ $appointment->formatted_appointment_time }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $appointment->status_color }}">
                                {{ $appointment->status_label }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @can('appointment.view')
                                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        <iconify-icon icon="lucide:eye" class="h-5 w-5"></iconify-icon>
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('No appointments found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
</div>

