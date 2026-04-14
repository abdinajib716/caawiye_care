<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-4xl">
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary">
                            <iconify-icon icon="lucide:edit" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Name') }}</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $supplier->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $supplier->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                                {{ ucfirst($supplier->status) }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Phone') }}</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $supplier->phone }}</p>
                    </div>

                    @if($supplier->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</label>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $supplier->email }}</p>
                        </div>
                    @endif

                    @if($supplier->address)
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Address') }}</label>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $supplier->address }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created') }}</label>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $supplier->created_at->format('M d, Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</label>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $supplier->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.backend-layout>
