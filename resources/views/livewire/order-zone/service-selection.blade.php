<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Select Services') }}
        </h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Step 1 of 3') }}
        </span>
    </div>

    <!-- Search Input -->
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <iconify-icon icon="lucide:search" class="h-5 w-5 text-gray-400"></iconify-icon>
        </div>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            class="form-control pl-10"
            placeholder="{{ __('Search services...') }}"
        />
        @if($search)
            <button
                type="button"
                wire:click="$set('search', '')"
                class="absolute inset-y-0 right-0 flex items-center pr-3"
            >
                <iconify-icon icon="lucide:x" class="h-5 w-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"></iconify-icon>
            </button>
        @endif
    </div>

    <!-- Services List -->
    <div class="max-h-[400px] space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        @forelse($services as $service)
            <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                <!-- Checkbox and Service Info -->
                <div class="flex flex-1 items-center space-x-3">
                    <input
                        type="checkbox"
                        wire:click="toggleService({{ $service->id }})"
                        @checked(in_array($service->id, $selectedServices))
                        class="form-checkbox h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                    />
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $service->name }}
                        </p>
                        @if($service->short_description)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ Str::limit($service->short_description, 60) }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Price and Quantity -->
                <div class="flex items-center space-x-4">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($service->price, 2) }}
                    </span>

                    @if(in_array($service->id, $selectedServices))
                        <div class="flex items-center space-x-2">
                            <button
                                type="button"
                                wire:click="decrementQuantity({{ $service->id }})"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                <iconify-icon icon="lucide:minus" class="h-4 w-4"></iconify-icon>
                            </button>
                            <input
                                type="number"
                                wire:model.blur="quantities.{{ $service->id }}"
                                wire:change="updateQuantity({{ $service->id }}, $event.target.value)"
                                min="1"
                                class="form-control w-16 text-center"
                                value="{{ $quantities[$service->id] ?? 1 }}"
                            />
                            <button
                                type="button"
                                wire:click="incrementQuantity({{ $service->id }})"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                <iconify-icon icon="lucide:plus" class="h-4 w-4"></iconify-icon>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <iconify-icon icon="lucide:package-search" class="mx-auto h-12 w-12 text-gray-400"></iconify-icon>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($search)
                        {{ __('No services found matching ":search"', ['search' => $search]) }}
                    @else
                        {{ __('No services available') }}
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Summary -->
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Selected Services') }}
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ count($selectedServices) }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Subtotal') }}
                </p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    ${{ number_format($subtotal, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
