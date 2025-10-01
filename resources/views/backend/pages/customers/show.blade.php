<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Customer Details') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @can('update', $customer)
                <x-buttons.button
                    variant="primary"
                    as="a"
                    href="{{ route('admin.customers.edit', $customer) }}"
                    icon="lucide:edit"
                >
                    {{ __('Edit Customer') }}
                </x-buttons.button>
            @endcan
            <x-buttons.button
                variant="secondary"
                as="a"
                href="{{ route('admin.customers.index') }}"
                icon="lucide:arrow-left"
            >
                {{ __('Back to Customers') }}
            </x-buttons.button>
        </div>
    </div>

    <!-- Customer Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Customer Information') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Customer Name') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Phone Number') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <span class="inline-flex items-center">
                                    <iconify-icon icon="lucide:phone" class="w-4 h-4 mr-2 text-gray-400"></iconify-icon>
                                    {{ $customer->formatted_phone }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Country Code') }}</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-200">
                                    {{ $customer->country_code }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                            <p class="mt-1">
                                @php
                                    $colorClass = match ($customer->status) {
                                        'active' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700',
                                        'inactive' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Address -->
                    @if($customer->address)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Address') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <span class="inline-flex items-start">
                                <iconify-icon icon="lucide:map-pin" class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0"></iconify-icon>
                                {{ $customer->address }}
                            </span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Details & Stats -->
        <div class="space-y-6">
            <!-- Customer Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Details') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Customer ID') }}</span>
                        <span class="text-sm font-mono text-gray-900 dark:text-white">#{{ $customer->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Phone') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $customer->formatted_phone }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Country') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $customer->country_code }}</span>
                    </div>
                    @if($customer->address)
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Address') }}</span>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Activity -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Activity') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Account Status') }}</span>
                        @if($customer->status === 'active')
                            <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                <iconify-icon icon="lucide:check-circle" class="w-4 h-4 mr-1"></iconify-icon>
                                {{ __('Active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center text-red-600 dark:text-red-400">
                                <iconify-icon icon="lucide:x-circle" class="w-4 h-4 mr-1"></iconify-icon>
                                {{ __('Inactive') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Created') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Last Updated') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $customer->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Days Since Created') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $customer->created_at->diffInDays(now()) }} {{ __('days') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.backend-layout>
