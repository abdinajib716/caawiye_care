<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Transaction Information -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Transaction Information') }}</h3>

                <div class="space-y-4">
                    <!-- Reference ID -->
                    <div class="flex items-start justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Reference ID') }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $transaction->reference_id }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                            @if($transaction->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @elseif($transaction->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                            @elseif($transaction->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                            @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>

                    <!-- Transaction ID -->
                    @if($transaction->transaction_id)
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Transaction ID') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->transaction_id }}</p>
                    </div>
                    @endif

                    <!-- Amount -->
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Amount') }}</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}
                        </p>
                    </div>

                    <!-- Payment Method & Provider -->
                    <div class="grid grid-cols-2 gap-4 border-b border-gray-200 pb-3 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Payment Method') }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->payment_method }}</p>
                        </div>
                        @if($transaction->provider)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Provider') }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->provider }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Customer Information -->
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer') }}</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $transaction->customer_name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $transaction->customer_phone }}</p>
                    </div>

                    <!-- Description -->
                    @if($transaction->description)
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                    </div>
                    @endif

                    <!-- Error Message -->
                    @if($transaction->error_message)
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-red-500 dark:text-red-400">{{ __('Error Message') }}</p>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $transaction->error_message }}</p>
                    </div>
                    @endif

                    <!-- Response Message -->
                    @if($transaction->response_message)
                    <div class="border-b border-gray-200 pb-3 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Response Message') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->response_message }}</p>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created At') }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($transaction->completed_at)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed At') }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->completed_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($transaction->failed_at)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Failed At') }}</p>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->failed_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Response Data -->
            @if($transaction->response_data)
            <div class="mt-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Provider Response Data') }}</h3>
                <pre class="overflow-x-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-800 dark:bg-gray-900 dark:text-gray-300">{{ json_encode($transaction->response_data, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>

        <!-- Related Order -->
        <div class="lg:col-span-1">
            @if($transaction->order)
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Related Order') }}</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Order Number') }}</p>
                        <a href="{{ route('admin.orders.show', $transaction->order) }}"
                            class="mt-1 text-sm font-semibold text-primary hover:underline">
                            {{ $transaction->order->order_number }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Order Total') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format((float) $transaction->order->total, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Order Status') }}</p>
                        <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($transaction->order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                            @elseif($transaction->order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                            @elseif($transaction->order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                            @endif">
                            {{ ucfirst($transaction->order->status) }}
                        </span>
                    </div>

                    @if($transaction->order->agent)
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Agent') }}</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->order->agent->name }}</p>
                    </div>
                    @endif

                    <div class="pt-3">
                        <a href="{{ route('admin.orders.show', $transaction->order) }}"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                            <iconify-icon icon="lucide:external-link" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('View Order Details') }}
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Related Order') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No order associated with this transaction yet.') }}</p>
            </div>
            @endif
        </div>
    </div>
</x-layouts.backend-layout>

