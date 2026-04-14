<div class="space-y-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        {{-- Total Requests --}}
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                        <iconify-icon icon="lucide:clipboard-list" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Requests') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['total']) }}</div>
                </div>
            </div>
        </x-card>

        {{-- Pending --}}
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-yellow-500 text-white">
                        <iconify-icon icon="lucide:clock" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['pending']) }}</div>
                </div>
            </div>
        </x-card>

        {{-- In Progress --}}
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                        <iconify-icon icon="lucide:loader" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('In Progress') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['in_progress']) }}</div>
                </div>
            </div>
        </x-card>

        {{-- Completed --}}
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                        <iconify-icon icon="lucide:check-circle" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statistics['completed']) }}</div>
                </div>
            </div>
        </x-card>

        {{-- Total Revenue --}}
        <x-card class="bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                        <iconify-icon icon="lucide:dollar-sign" class="h-5 w-5"></iconify-icon>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Revenue') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($statistics['total_revenue'], 2) }}</div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filters --}}
    <x-card class="bg-white dark:bg-gray-800">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control w-full" placeholder="{{ __('Search by ID, customer, patient, provider...') }}">
            </div>

            {{-- Status Filter --}}
            <div class="w-40">
                <select wire:model.live="status" class="form-control w-full">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="in_progress">{{ __('In Progress') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                </select>
            </div>

            {{-- Payment Status Filter --}}
            <div class="w-40">
                <select wire:model.live="paymentStatus" class="form-control w-full">
                    <option value="">{{ __('All Payments') }}</option>
                    <option value="verified">{{ __('Verified') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="failed">{{ __('Failed') }}</option>
                </select>
            </div>

            {{-- New Booking Button --}}
            <a href="{{ route('admin.collections.create') }}" class="btn btn-primary">
                <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                {{ __('New Request') }}
            </a>
        </div>
    </x-card>

    {{-- Messages --}}
    @if(session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Data Table --}}
    <x-card class="bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Request ID') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Customer') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Patient') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Provider') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Assigned Staff') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Delivery') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Payment') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse ($collections as $collection)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ $collection->request_id }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $collection->customer_name }}</p>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $collection->customer_phone }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 dark:text-white">
                                <div>
                                    <p>{{ $collection->patient_name }}</p>
                                    @if($collection->medicineOrder)
                                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('From') }} {{ $collection->medicineOrder->order_number }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $collection->provider_name }}</p>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $collection->provider_type_label }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $collection->assignedStaff->first_name ?? 'N/A' }} {{ $collection->assignedStaff->last_name ?? '' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                @if($collection->delivery_required)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        {{ __('Yes') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ __('No') }}
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($collection->total_amount, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                @php
                                    $paymentColors = [
                                        'verified' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $paymentColors[$collection->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $collection->payment_status_label }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$collection->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $collection->status_label }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $collection->created_at->format('M d, Y') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Status Actions --}}
                                    @if($collection->status === 'pending')
                                        <button wire:click="startProgress({{ $collection->id }})" 
                                            wire:confirm="{{ __('Start processing this request?') }}"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                            title="{{ __('Start Progress') }}">
                                            <iconify-icon icon="lucide:play" class="h-4 w-4"></iconify-icon>
                                        </button>
                                    @elseif($collection->status === 'in_progress')
                                        <button wire:click="markCompleted({{ $collection->id }})" 
                                            wire:confirm="{{ __('Mark this request as completed?') }}"
                                            class="inline-flex items-center justify-center rounded-lg p-2 text-green-600 hover:bg-green-50 hover:text-green-800 dark:text-green-400 dark:hover:bg-green-900/20"
                                            title="{{ __('Mark Completed') }}">
                                            <iconify-icon icon="lucide:check-circle" class="h-4 w-4"></iconify-icon>
                                        </button>
                                    @endif

                                    {{-- View Details --}}
                                    <a href="{{ route('admin.collections.show', $collection->id) }}" 
                                       class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                       title="{{ __('View Details') }}">
                                        <iconify-icon icon="lucide:eye" class="h-4 w-4"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center">
                                <iconify-icon icon="lucide:inbox" class="mx-auto mb-2 h-12 w-12 text-gray-300 dark:text-gray-600"></iconify-icon>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No report collection requests found.') }}</p>
                                <a href="{{ route('admin.collections.create') }}" class="mt-4 inline-flex btn btn-primary">
                                    <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                                    {{ __('Create New Request') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($collections->hasPages())
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                {{ $collections->links() }}
            </div>
        @endif
    </x-card>
</div>
