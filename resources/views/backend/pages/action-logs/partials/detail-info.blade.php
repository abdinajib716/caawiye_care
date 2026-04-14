@php
    $logData = json_decode($log->data, true) ?? [];
    // Filter out sensitive data
    $displayData = collect($logData)->except(['password', 'remember_token', 'email_verified_at', 'action_by'])->toArray();
@endphp

<div x-data="{ modalOpen: false }" class="inline-block">
    <button 
        type="button" 
        @click="modalOpen = true"
        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/30"
    >
        <iconify-icon icon="lucide:eye" class="w-4 h-4 mr-1.5"></iconify-icon>
        {{ __('View Details') }}
    </button>

    <!-- Modal -->
    <div 
        x-show="modalOpen" 
        x-cloak
        class="fixed inset-0 z-[9999] overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div class="flex min-h-screen items-center justify-center px-4 py-6">
            <!-- Backdrop -->
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="modalOpen = false"
                class="fixed inset-0 bg-gray-900/75 dark:bg-gray-900/90 transition-opacity" 
                aria-hidden="true"
            ></div>
            
            <!-- Modal Content -->
            <div 
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-10 w-full max-w-2xl transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-xl transition-all"
            >
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                            {{ __('Action Log Details') }}
                        </h3>
                        <button 
                            type="button" 
                            @click="modalOpen = false"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors"
                        >
                            <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="bg-white dark:bg-gray-800 px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <!-- Log Info -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Action Type') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ ucfirst($log->type) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Performed By') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->user->full_name ?? __('System') }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Action') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->title }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date & Time') }}</p>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data Details -->
                    @if(!empty($displayData))
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Details') }}</h4>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                @foreach($displayData as $key => $value)
                                    @if(is_array($value))
                                        <!-- Nested object/array -->
                                        <div class="mb-3 last:mb-0">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                                                {{ __(str_replace('_', ' ', $key)) }}
                                            </p>
                                            <div class="ml-4 space-y-2">
                                                @foreach($value as $subKey => $subValue)
                                                    @if(!in_array($subKey, ['password', 'remember_token', 'email_verified_at', 'avatar_id', 'avatar_url']))
                                                        <div class="flex items-start">
                                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[120px]">
                                                                {{ __(str_replace('_', ' ', ucfirst($subKey))) }}:
                                                            </span>
                                                            <span class="text-sm text-gray-900 dark:text-white ml-2">
                                                                @if(is_bool($subValue))
                                                                    {{ $subValue ? __('Yes') : __('No') }}
                                                                @elseif(is_null($subValue))
                                                                    <span class="text-gray-400">{{ __('N/A') }}</span>
                                                                @elseif(is_array($subValue))
                                                                    <span class="text-gray-500 text-xs">{{ count($subValue) }} {{ __('items') }}</span>
                                                                @else
                                                                    {{ $subValue }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif(!in_array($key, ['password', 'remember_token', 'email_verified_at']))
                                        <!-- Simple key-value -->
                                        <div class="flex items-start mb-2 last:mb-0">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[120px]">
                                                {{ __(str_replace('_', ' ', ucfirst($key))) }}:
                                            </span>
                                            <span class="text-sm text-gray-900 dark:text-white ml-2">
                                                @if(is_bool($value))
                                                    {{ $value ? __('Yes') : __('No') }}
                                                @elseif(is_null($value))
                                                    <span class="text-gray-400">{{ __('N/A') }}</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <iconify-icon icon="lucide:info" class="w-12 h-12 text-gray-400 mx-auto mb-2"></iconify-icon>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No additional details available') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex justify-end">
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                    >
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


