@php
    $breadcrumbs = [
        ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
        ['label' => __('Services'), 'url' => route('admin.services.index')],
        ['label' => $service->name, 'url' => null],
    ];
@endphp

<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $service->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Service Details') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.services.edit', $service) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-blue-500 dark:hover:bg-blue-600">
                <iconify-icon icon="lucide:edit" class="w-4 h-4 mr-2"></iconify-icon>
                {{ __('Edit Service') }}
            </a>
            <a href="{{ route('admin.services.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-700 dark:hover:bg-gray-600">
                <iconify-icon icon="lucide:arrow-left" class="w-4 h-4 mr-2"></iconify-icon>
                {{ __('Back to Services') }}
            </a>
        </div>
    </div>

    <!-- Service Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Service Information') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Service Name') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $service->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Category') }}</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($service->category)
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:text-gray-200">
                                        {{ $service->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">{{ __('No Category') }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                            <p class="mt-1">
                                @php
                                    $colorClass = match ($service->status) {
                                        'active' => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700',
                                        'inactive' => 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700',
                                        'discontinued' => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $colorClass }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($service->short_description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Short Description') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $service->short_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pricing & Stats -->
        <div class="space-y-6">
            <!-- Pricing Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Pricing') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Price') }}</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format((float) $service->price, 2) }}</span>
                    </div>
                    @if((float) $service->cost > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Cost') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">${{ number_format((float) $service->cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Profit Margin') }}</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format((float) $service->profit_margin, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Profit %') }}</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ number_format((float) $service->profit_percentage, 1) }}%</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Service Features -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Features') }}</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Featured Service') }}</span>
                        @if($service->is_featured)
                            <span class="inline-flex items-center text-yellow-600 dark:text-yellow-400">
                                <iconify-icon icon="lucide:star" class="w-4 h-4 mr-1"></iconify-icon>
                                {{ __('Yes') }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">{{ __('No') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Created') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Last Updated') }}</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $service->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
