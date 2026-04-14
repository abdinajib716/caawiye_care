<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-card class="bg-white dark:bg-gray-800">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $labTest->name }}</h2>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold 
                    {{ $labTest->bill_to === 'provider' ? 'bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800' : 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800' }}">
                    {{ $labTest->bill_to === 'provider' ? __('Bill Provider') : __('Bill Customer') }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Test Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        {{ __('Test Information') }}
                    </h3>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Provider') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <a href="{{ route('admin.providers.show', $labTest->provider) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $labTest->provider->name }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $labTest->description ?? '-' }}</p>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                        {{ __('Pricing Information') }}
                    </h3>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cost') }}</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">${{ number_format((float)$labTest->cost, 2) }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Commission') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ $labTest->commission_percentage }}% 
                            <span class="text-sm text-gray-500">({{ $labTest->commission_amount_formatted }})</span>
                        </p>
                    </div>

                    @if($labTest->commission_type === 'bill_provider')
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Provider Payment') }}</label>
                            <p class="mt-1 text-lg font-semibold text-orange-600 dark:text-orange-400">
                                {{ $labTest->provider_payment_formatted }}
                            </p>
                            <p class="text-xs text-gray-500">{{ __('(Cost - Commission)') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer Pays') }}</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $labTest->cost_formatted }}
                            </p>
                        </div>
                    @else
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total (Cost + Commission)') }}</label>
                            <p class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">
                                {{ $labTest->total_with_commission_formatted }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer Pays') }}</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $labTest->total_with_commission_formatted }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Information -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('System Information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="text-gray-500 dark:text-gray-400">{{ __('Created At') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $labTest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="text-gray-500 dark:text-gray-400">{{ __('Updated At') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $labTest->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <a href="{{ route('admin.lab-tests.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <iconify-icon icon="lucide:arrow-left" class="w-4 h-4 mr-2"></iconify-icon>
                    {{ __('Back to List') }}
                </a>

                <div class="flex items-center space-x-3">
                    @can('lab_test.edit')
                        <a href="{{ route('admin.lab-tests.edit', $labTest) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <iconify-icon icon="lucide:edit" class="w-4 h-4 mr-2"></iconify-icon>
                            {{ __('Edit Test') }}
                        </a>
                    @endcan

                    @can('lab_test.delete')
                        <form method="POST" action="{{ route('admin.lab-tests.destroy', $labTest) }}" 
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this lab test?') }}');">
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
