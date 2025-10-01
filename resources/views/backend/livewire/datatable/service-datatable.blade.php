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
                    placeholder="{{ __('Search services...') }}"
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
            <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Category Filter -->
                <x-inputs.select
                    wire:model.live="filters.category_id"
                    :options="$filterOptions['category_id']"
                    placeholder="{{ __('All Categories') }}"
                    label="{{ __('Category') }}"
                />

                <!-- Status Filter -->
                <x-inputs.select
                    wire:model.live="filters.status"
                    :options="$filterOptions['status']"
                    placeholder="{{ __('All Statuses') }}"
                    label="{{ __('Status') }}"
                />

                <!-- Featured Filter -->
                <x-inputs.select
                    wire:model.live="filters.is_featured"
                    :options="$filterOptions['is_featured']"
                    placeholder="{{ __('All Services') }}"
                    label="{{ __('Featured') }}"
                />

                <!-- Price Range -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Price Range') }}</label>
                    <div class="flex space-x-2">
                        <x-inputs.input
                            wire:model.live.debounce.500ms="filters.price_min"
                            type="number"
                            step="0.01"
                            placeholder="{{ __('Min') }}"
                            class="w-full"
                        />
                        <x-inputs.input
                            wire:model.live.debounce.500ms="filters.price_max"
                            type="number"
                            step="0.01"
                            placeholder="{{ __('Max') }}"
                            class="w-full"
                        />
                    </div>
                </div>

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
                    {{ __(':count services selected', ['count' => count($selectedItems)]) }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                @can('update', App\Models\Service::class)
                    <x-buttons.button variant="secondary" wire:click="bulkActivate" size="sm">
                        {{ __('Activate') }}
                    </x-buttons.button>
                    <x-buttons.button variant="secondary" wire:click="bulkDeactivate" size="sm">
                        {{ __('Deactivate') }}
                    </x-buttons.button>
                    <x-buttons.button variant="secondary" wire:click="bulkFeature" size="sm">
                        {{ __('Feature') }}
                    </x-buttons.button>
                @endcan
                @can('delete', App\Models\Service::class)
                    <x-buttons.button variant="danger" wire:click="bulkDelete" size="sm">
                        {{ __('Delete') }}
                    </x-buttons.button>
                @endcan
            </div>
        </div>
    @endif

    <!-- Services Table -->
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

                        <!-- Service Name -->
                        <th class="w-2/5 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>{{ __('Service Name') }}</span>
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <iconify-icon icon="lucide:chevron-up" class="h-4 w-4"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:chevron-down" class="h-4 w-4"></iconify-icon>
                                    @endif
                                @endif
                            </button>
                        </th>

                        <!-- Category -->
                        <th class="w-32 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Category') }}
                        </th>

                        <!-- Price -->
                        <th class="w-24 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <button wire:click="sortBy('price')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>{{ __('Price') }}</span>
                                @if($sortField === 'price')
                                    @if($sortDirection === 'asc')
                                        <iconify-icon icon="lucide:chevron-up" class="h-4 w-4"></iconify-icon>
                                    @else
                                        <iconify-icon icon="lucide:chevron-down" class="h-4 w-4"></iconify-icon>
                                    @endif
                                @endif
                            </button>
                        </th>

                        <!-- Profit -->
                        <th class="w-24 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Profit') }}
                        </th>

                        <!-- Status -->
                        <th class="w-20 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Status') }}
                        </th>

                        <!-- Created -->
                        <th class="w-20 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
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


                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50">
                            <!-- Select -->
                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedItems"
                                    value="{{ $service->id }}"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                            </td>

                            <!-- Service Name -->
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $service->name }}</div>
                                        @if($service->short_description)
                                            <div class="text-sm text-gray-500 truncate">{{ Str::limit($service->short_description, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-4 py-4">
                                @if($service->category)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $service->category->name }}</div>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-2 h-2 bg-gray-300 rounded-full mr-2"></div>
                                        <div class="text-sm text-gray-400">{{ __('No Category') }}</div>
                                    </div>
                                @endif
                            </td>

                            <!-- Price -->
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">${{ number_format((float) $service->price, 2) }}</div>
                                @if((float) $service->cost > 0)
                                    <div class="text-xs text-gray-500">{{ __('Cost: $:cost', ['cost' => number_format((float) $service->cost, 2)]) }}</div>
                                @endif
                            </td>

                            <!-- Profit -->
                            <td class="px-4 py-4">
                                @if((float) $service->cost > 0)
                                    <div class="text-sm font-medium text-green-600">${{ number_format((float) $service->profit_margin, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format((float) $service->profit_percentage, 1) }}%</div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                @php
                                    $colorClass = match ($service->status) {
                                        'active' => 'bg-green-100 text-green-800 border border-green-200',
                                        'inactive' => 'bg-red-100 text-red-800 border border-red-200',
                                        'discontinued' => 'bg-gray-100 text-gray-800 border border-gray-200',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>

                            <!-- Created -->
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $service->created_at->format('d M') }}</div>
                                <div class="text-xs text-gray-500">{{ $service->created_at->format('Y') }}</div>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <iconify-icon icon="lucide:grid-3x3" class="h-12 w-12 text-gray-400"></iconify-icon>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No services found') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Use the "Add Service" button above to create your first service.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
            <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                {{ $services->links() }}
            </div>
        @endif
    </div>


</div>
