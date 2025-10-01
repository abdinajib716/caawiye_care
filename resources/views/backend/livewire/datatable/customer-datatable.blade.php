<!-- Livewire Component Content -->
<div>

    <!-- Filters and Search -->
    <div class="mb-6 space-y-4">
        <!-- Search and Quick Filters -->
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-md">
                <x-inputs.input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search customers...') }}"
                    class="w-full"
                >
                    <x-slot name="prepend">
                        <iconify-icon icon="lucide:search" class="h-5 w-5 text-gray-400"></iconify-icon>
                    </x-slot>
                </x-inputs.input>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">
                <!-- Per Page -->
                <x-inputs.select
                    wire:model.live="perPage"
                    :options="[10 => '10', 15 => '15', 25 => '25', 50 => '50']"
                    class="w-20"
                />

                <!-- Filters Toggle -->
                <x-buttons.button variant="secondary" wire:click="$toggle('showFilters')">
                    <iconify-icon icon="lucide:filter" class="h-4 w-4"></iconify-icon>
                    {{ __('Filters') }}
                </x-buttons.button>
            </div>
        </div>

        <!-- Advanced Filters -->
        @if($showFilters)
            <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Status Filter -->
                <x-inputs.select
                    wire:model.live="filters.status"
                    :options="$filterOptions['status']"
                    placeholder="{{ __('All Statuses') }}"
                    label="{{ __('Status') }}"
                />

                <!-- Country Code Filter -->
                <x-inputs.select
                    wire:model.live="filters.country_code"
                    :options="$filterOptions['country_code']"
                    placeholder="{{ __('All Countries') }}"
                    label="{{ __('Country') }}"
                />

                <!-- Clear Filters -->
                <div class="flex items-end">
                    <x-buttons.button variant="secondary" wire:click="resetFilters" size="sm" class="w-full">
                        {{ __('Clear Filters') }}
                    </x-buttons.button>
                </div>
            </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    @if(count($selectedItems) > 0)
        <div class="mb-4 flex items-center justify-between rounded-lg bg-blue-50 p-4">
            <div class="flex items-center">
                <span class="text-sm font-medium text-blue-900">
                    {{ __(':count customers selected', ['count' => count($selectedItems)]) }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                @can('update', App\Models\Customer::class)
                    <x-buttons.button variant="secondary" wire:click="bulkActivate" size="sm">
                        {{ __('Activate') }}
                    </x-buttons.button>
                    <x-buttons.button variant="secondary" wire:click="bulkDeactivate" size="sm">
                        {{ __('Deactivate') }}
                    </x-buttons.button>
                @endcan
                @can('delete', App\Models\Customer::class)
                    <x-buttons.button variant="danger" wire:click="bulkDelete" size="sm">
                        {{ __('Delete') }}
                    </x-buttons.button>
                @endcan
            </div>
        </div>
    @endif

    <!-- Customers Table -->
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <!-- Select All -->
                        <th class="w-12 px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                        </th>

                        <!-- Customer Name -->
                        <th class="w-2/5 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>{{ __('Customer Name') }}</span>
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <iconify-icon icon="lucide:chevron-up" class="h-4 w-4"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:chevron-down" class="h-4 w-4"></iconify-icon>
                                    @endif
                                @endif
                            </button>
                        </th>

                        <!-- Phone -->
                        <th class="w-1/5 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('phone')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>{{ __('Phone') }}</span>
                                @if($sortField === 'phone')
                                    @if($sortDirection === 'asc')
                                        <iconify-icon icon="lucide:chevron-up" class="h-4 w-4"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:chevron-down" class="h-4 w-4"></iconify-icon>
                                    @endif
                                @endif
                            </button>
                        </th>

                        <!-- Status -->
                        <th class="w-1/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Status') }}
                        </th>

                        <!-- Created -->
                        <th class="w-1/8 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>{{ __('Created') }}</span>
                                @if($sortField === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <iconify-icon icon="lucide:chevron-up" class="h-4 w-4"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:chevron-down" class="h-4 w-4"></iconify-icon>
                                    @endif
                                @endif
                            </button>
                        </th>

                        <!-- Actions -->
                        <th class="w-1/12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <!-- Select -->
                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedItems"
                                    value="{{ $customer->id }}"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                            </td>

                            <!-- Customer Name -->
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $customer->name }}</div>
                                        @if($customer->address)
                                            <div class="text-sm text-gray-500 truncate">{{ Str::limit($customer->address, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Phone -->
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $customer->formatted_phone }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                @php
                                    $colorClass = match ($customer->status) {
                                        'active' => 'bg-green-100 text-green-800 border border-green-200',
                                        'inactive' => 'bg-red-100 text-red-800 border border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>

                            <!-- Created -->
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $customer->created_at->format('d M') }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->created_at->format('Y') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    @can('view', $customer)
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200" title="{{ __('View Customer') }}">
                                            <iconify-icon icon="lucide:eye" class="w-4 h-4"></iconify-icon>
                                        </a>
                                    @endcan
                                    @can('update', $customer)
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-700 transition-colors duration-200" title="{{ __('Edit Customer') }}">
                                            <iconify-icon icon="lucide:edit" class="w-4 h-4"></iconify-icon>
                                        </a>
                                    @endcan
                                    @can('delete', $customer)
                                        <button wire:click="deleteItem({{ $customer->id }})" class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200" title="{{ __('Delete Customer') }}">
                                            <iconify-icon icon="lucide:trash" class="w-4 h-4"></iconify-icon>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <iconify-icon icon="lucide:users" class="h-12 w-12 text-gray-400"></iconify-icon>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No customers found') }}</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
