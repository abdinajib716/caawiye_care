@props([
    'label' => '',
    'placeholder' => 'Search...',
    'searchModel' => '',
    'options' => [],
    'selectedValue' => null,
    'selectedDisplay' => '',
    'onSelect' => '',
    'required' => false,
    'icon' => 'lucide:search',
])

<div class="space-y-1" x-data="{ isOpen: false }" @click.away="isOpen = false">
    @if($label)
        <label class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <!-- Dropdown Button -->
        <button
            type="button"
            @click="isOpen = !isOpen"
            class="form-control flex items-center justify-between w-full text-left"
            :class="{ 'ring-2 ring-blue-500 border-blue-500': isOpen }"
        >
            <span class="flex items-center flex-1 truncate">
                @if($selectedValue)
                    <iconify-icon icon="lucide:check-circle" class="h-4 w-4 mr-2 text-green-600"></iconify-icon>
                    <span class="text-gray-900 dark:text-white">{{ $selectedDisplay }}</span>
                @else
                    <iconify-icon icon="{{ $icon }}" class="h-4 w-4 mr-2 text-gray-400"></iconify-icon>
                    <span class="text-gray-500 dark:text-gray-400">{{ $placeholder }}</span>
                @endif
            </span>
            <iconify-icon 
                icon="lucide:chevron-down" 
                class="h-5 w-5 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
            ></iconify-icon>
        </button>

        <!-- Dropdown Panel -->
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-hidden"
            style="display: none;"
        >
            <!-- Search Input -->
            <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="relative">
                    <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="{{ $searchModel }}"
                        class="form-control pl-9 w-full"
                        placeholder="{{ __('Type to search...') }}"
                        @click.stop
                    >
                </div>
            </div>

            <!-- Options List -->
            <div class="max-h-60 overflow-y-auto">
                @if(count($options) > 0)
                    @foreach($options as $option)
                        <button
                            type="button"
                            wire:click="{{ $onSelect }}({{ $option['id'] }})"
                            @click="isOpen = false"
                            class="w-full px-4 py-3 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                            :class="{ 'bg-blue-50 dark:bg-blue-900/20': {{ $selectedValue }} === {{ $option['id'] }} }"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-white truncate">
                                        {{ $option['name'] }}
                                    </div>
                                    @if(isset($option['phone']))
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                            <iconify-icon icon="lucide:phone" class="h-3 w-3 inline"></iconify-icon>
                                            {{ $option['phone'] }}
                                        </div>
                                    @endif
                                    @if(isset($option['email']))
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                            <iconify-icon icon="lucide:mail" class="h-3 w-3 inline"></iconify-icon>
                                            {{ $option['email'] }}
                                        </div>
                                    @endif
                                </div>
                                @if($selectedValue === $option['id'])
                                    <iconify-icon icon="lucide:check" class="h-5 w-5 text-blue-600 dark:text-blue-400 ml-2 flex-shrink-0"></iconify-icon>
                                @endif
                            </div>
                        </button>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <iconify-icon icon="lucide:search-x" class="h-12 w-12 mx-auto mb-2 opacity-50"></iconify-icon>
                        <p class="text-sm">{{ __('No results found') }}</p>
                        <p class="text-xs mt-1">{{ __('Try adjusting your search') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
