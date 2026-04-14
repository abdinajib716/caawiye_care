<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="space-y-6">
        <!-- Delivery Locations Section -->
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Delivery Locations') }}</h3>
                </div>
            </x-slot>

            <!-- Add Location Form -->
            <form action="{{ route('admin.delivery-settings.locations.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="flex gap-3">
                    <input type="text" name="name" placeholder="{{ __('Location name (e.g., Hodan, Dharkenly)') }}" class="form-control flex-1 @error('name') border-red-500 @enderror" required>
                    <button type="submit" class="btn btn-primary whitespace-nowrap">
                        <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Add Location') }}
                    </button>
                </div>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </form>

            <!-- Locations List -->
            @if ($locations->count() > 0)
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($locations as $location)
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $location->name }}</span>
                            <form action="{{ route('admin.delivery-settings.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400">
                                    <iconify-icon icon="lucide:trash-2" class="h-4 w-4"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('No locations added yet') }}</p>
            @endif
        </x-card>

        <!-- Delivery Prices Section -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Route Pricing') }}</h3>
            </x-slot>

            <!-- Add Price Form -->
            @if ($locations->count() >= 2)
                <form action="{{ route('admin.delivery-settings.prices.store') }}" method="POST" class="mb-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                        <select name="pickup_location_id" class="form-control @error('pickup_location_id') border-red-500 @enderror" required>
                            <option value="">{{ __('Pick-up Location') }}</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <select name="dropoff_location_id" class="form-control @error('dropoff_location_id') border-red-500 @enderror" required>
                            <option value="">{{ __('Drop-off Location') }}</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="price" step="0.01" min="0" placeholder="{{ __('Price ($)') }}" class="form-control @error('price') border-red-500 @enderror" required>
                        <button type="submit" class="btn btn-primary whitespace-nowrap">
                            <iconify-icon icon="lucide:plus" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Add Price') }}
                        </button>
                    </div>
                    @if ($errors->any())
                        <p class="mt-1 text-sm text-red-600">{{ $errors->first() }}</p>
                    @endif
                </form>
            @else
                <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">{{ __('Add at least 2 locations first to configure pricing') }}</p>
                </div>
            @endif

            <!-- Prices List -->
            @if ($prices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Route') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Price') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($prices as $price)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $price->pickupLocation->name }} <span class="text-gray-400">→</span> {{ $price->dropoffLocation->name }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($price->price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('admin.delivery-settings.prices.destroy', $price) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400">
                                                <iconify-icon icon="lucide:trash-2" class="h-4 w-4"></iconify-icon>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('No route prices configured yet') }}</p>
            @endif
        </x-card>
    </div>
</x-layouts.backend-layout>
