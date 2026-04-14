<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Refund') }} #{{ $refund->refund_number }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Refund Details -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Refund Details') }}</h3>
                
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Refund Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $refund->refund_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Order Type') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ class_basename($refund->order_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Original Amount') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $refund->formatted_original_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Refund Amount') }}</dt>
                        <dd class="mt-1 text-lg font-bold text-red-600 dark:text-red-400">{{ $refund->formatted_refund_amount }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Reason') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $refund->reason }}</dd>
                    </div>
                </dl>
            </x-card>

            <!-- Order Information -->
            @if($order)
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Related Order') }}</h3>
                
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Order Number') }}</dt>
                        <dd class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                            {{ $order->order_number ?? $order->booking_number ?? "#{$order->id}" }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->customer?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->status_label ?? $order->status }}</dd>
                    </div>
                </dl>
            </x-card>
            @endif

            <!-- Provider Payment Status -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Provider Payment Status') }}</h3>
                
                <div class="flex items-center gap-4">
                    @if($refund->provider_payment_reversed)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            <iconify-icon icon="lucide:check-circle" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Provider Payment Reversed') }}
                        </span>
                        @if($refund->provider_refund_confirmed_at)
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Confirmed at:') }} {{ $refund->provider_refund_confirmed_at->format('M d, Y H:i') }}
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            <iconify-icon icon="lucide:alert-circle" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Provider Payment NOT Reversed') }}
                        </span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Refund cannot be approved until provider payment is reversed.') }}
                        </p>
                    @endif
                </div>
            </x-card>

            <!-- History -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('History') }}</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                            <iconify-icon icon="lucide:plus" class="h-4 w-4"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('Requested by') }} {{ $refund->requestedBy?->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </li>
                    @if($refund->approved_at)
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $refund->isRejected() ? 'bg-red-100 text-red-600 dark:bg-red-900/30' : 'bg-green-100 text-green-600 dark:bg-green-900/30' }}">
                                <iconify-icon icon="lucide:{{ $refund->isRejected() ? 'x' : 'check' }}" class="h-4 w-4"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $refund->isRejected() ? __('Rejected by') : __('Approved by') }} {{ $refund->approvedBy?->name ?? 'System' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->approved_at->format('M d, Y H:i') }}</p>
                            </div>
                        </li>
                    @endif
                    @if($refund->processed_at)
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-600 dark:bg-orange-900/30">
                                <iconify-icon icon="lucide:loader-2" class="h-4 w-4"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ __('Processing started by') }} {{ $refund->processedBy?->name ?? 'System' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->processed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </li>
                    @endif
                    @if($refund->refund_executed_at)
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30">
                                <iconify-icon icon="lucide:banknote" class="h-4 w-4"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ __('Refund completed') }} ({{ $refund->refund_method }})</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->refund_executed_at->format('M d, Y H:i') }}</p>
                                @if($refund->refund_reference)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Reference:') }} {{ $refund->refund_reference }}</p>
                                @endif
                            </div>
                        </li>
                    @endif
                </ul>
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</h3>
                <div class="text-center">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold bg-{{ $refund->status_color }}-100 text-{{ $refund->status_color }}-800 dark:bg-{{ $refund->status_color }}-900/30 dark:text-{{ $refund->status_color }}-400">
                        {{ $refund->status_label }}
                    </span>
                </div>

                @if($refund->isRejected() && $refund->rejection_reason)
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                        <p class="text-sm font-medium text-red-800 dark:text-red-400">{{ __('Rejection Reason') }}</p>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $refund->rejection_reason }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Actions Card -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Actions') }}</h3>
                <div class="space-y-3">
                    @if($refund->isPending() && !$refund->provider_payment_reversed)
                        <form action="{{ route('admin.refunds.confirm-provider-reversed', $refund) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-full">
                                <iconify-icon icon="lucide:refresh-cw" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Confirm Provider Reversed') }}
                            </button>
                        </form>
                    @endif

                    @if($refund->isPending() && $refund->provider_payment_reversed)
                        <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-full">
                                <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Approve Refund') }}
                            </button>
                        </form>

                        <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="btn btn-danger w-full">
                            <iconify-icon icon="lucide:x" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Reject Refund') }}
                        </button>
                    @endif

                    @if($refund->isApproved())
                        <form action="{{ route('admin.refunds.process', $refund) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full">
                                <iconify-icon icon="lucide:play" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Start Processing') }}
                            </button>
                        </form>
                    @endif

                    @if($refund->isProcessing())
                        <button type="button" onclick="document.getElementById('completeModal').classList.remove('hidden')" class="btn btn-success w-full">
                            <iconify-icon icon="lucide:check-circle" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Complete Refund') }}
                        </button>
                    @endif

                    <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary w-full">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
            <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Reject Refund') }}</h3>
                <form action="{{ route('admin.refunds.reject', $refund) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Reason for Rejection') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="btn btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Reject Refund') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div id="completeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('completeModal').classList.add('hidden')"></div>
            <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Complete Refund') }}</h3>
                <form action="{{ route('admin.refunds.complete', $refund) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="refund_method" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Refund Method') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="refund_method" name="refund_method" class="form-control" required>
                            <option value="evc">{{ __('EVC Plus') }}</option>
                            <option value="edahab">{{ __('E-Dahab') }}</option>
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="bank">{{ __('Bank Transfer') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="refund_reference" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Reference Number') }}
                        </label>
                        <input type="text" id="refund_reference" name="refund_reference" class="form-control" placeholder="{{ __('Transaction reference...') }}">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('completeModal').classList.add('hidden')" class="btn btn-secondary">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('Complete Refund') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
