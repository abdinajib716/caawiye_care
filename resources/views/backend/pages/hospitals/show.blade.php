<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $hospital->name }}</h1>
            <div class="flex space-x-3">
                @can('hospital.edit')
                    <x-buttons.button variant="secondary" as="a" href="{{ route('admin.hospitals.edit', $hospital) }}">
                        <iconify-icon icon="lucide:edit" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Edit') }}
                    </x-buttons.button>
                @endcan
            </div>
        </div>

        <!-- Hospital Details -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Hospital Information') }}</h3>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Name') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->name }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Status') }}</p>
                    <p class="mt-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $hospital->status_color }}">
                            {{ $hospital->status_label }}
                        </span>
                    </p>
                </div>

                @if($hospital->phone)
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Phone') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->phone }}</p>
                </div>
                @endif

                @if($hospital->email)
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Email') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->email }}</p>
                </div>
                @endif

                @if($hospital->address)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500">{{ __('Address') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->address }}</p>
                </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Created At') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->created_at->format('M j, Y \a\t g:i A') }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</p>
                    <p class="mt-1 text-base text-gray-900">{{ $hospital->updated_at->format('M j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.backend-layout>

