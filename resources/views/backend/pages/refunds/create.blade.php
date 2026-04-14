<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-3xl space-y-6">
        <x-card class="bg-white dark:bg-gray-800">
            <div class="p-6 space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Initiate Refund') }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Create a refund request for an eligible paid order.') }}</p>
                </div>

                @if(session('error'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.refunds.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <input type="hidden" name="order_type" value="{{ old('order_type', $orderType) }}">
                    <input type="hidden" name="order_id" value="{{ old('order_id', $order?->id) }}">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Order') }}</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order?->order_number ?? __('Order not found') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Paid') }}</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">${{ number_format((float) ($order?->total ?? 0), 2) }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="refund_amount" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Refund Amount') }}</label>
                        <input
                            id="refund_amount"
                            name="refund_amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            max="{{ (float) ($order?->total ?? 0) }}"
                            value="{{ old('refund_amount', number_format((float) ($order?->total ?? 0), 2, '.', '')) }}"
                            class="form-control w-full"
                            required
                        >
                        @error('refund_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reason" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Reason') }}</label>
                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            class="form-control-textarea w-full"
                            placeholder="{{ __('Explain why this order is being refunded') }}"
                            required
                        >{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">
                            <iconify-icon icon="lucide:rotate-ccw" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Create Refund Request') }}
                        </button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-layouts.backend-layout>
