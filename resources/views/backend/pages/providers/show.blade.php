<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card class="bg-white dark:bg-gray-800">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $provider->name }}</h2>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-{{ $provider->status_color }}-100 text-{{ $provider->status_color }}-800 border border-{{ $provider->status_color }}-200 dark:bg-{{ $provider->status_color }}-900/20 dark:text-{{ $provider->status_color }}-400 dark:border-{{ $provider->status_color }}-800">
                    {{ $provider->status_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        {{ __('Contact Information') }}
                    </h3>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Phone Number') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $provider->phone }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $provider->email ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Address') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $provider->address ?? '-' }}</p>
                    </div>
                </div>

                <!-- System Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        {{ __('System Information') }}
                    </h3>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-{{ $provider->status_color }}-100 text-{{ $provider->status_color }}-800 border border-{{ $provider->status_color }}-200 dark:bg-{{ $provider->status_color }}-900/20 dark:text-{{ $provider->status_color }}-400 dark:border-{{ $provider->status_color }}-800">
                                {{ $provider->status_label }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created At') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $provider->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Updated At') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $provider->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <a href="{{ route('admin.providers.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <iconify-icon icon="lucide:arrow-left" class="w-4 h-4 mr-2"></iconify-icon>
                    {{ __('Back to List') }}
                </a>

                <div class="flex items-center space-x-3">
                    @can('provider.edit')
                        <a href="{{ route('admin.providers.edit', $provider) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <iconify-icon icon="lucide:edit" class="w-4 h-4 mr-2"></iconify-icon>
                            {{ __('Edit Provider') }}
                        </a>
                    @endcan

                    @can('provider.delete')
                        <form method="POST" action="{{ route('admin.providers.destroy', $provider) }}" 
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this provider?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                <iconify-icon icon="lucide:trash" class="w-4 h-4 mr-2"></iconify-icon>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </x-card>
</x-layouts.backend-layout>
