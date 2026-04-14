<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Expense') }} #{{ $expense->expense_number }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Expense Details') }}</h3>
                
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Expense Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->expense_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->expense_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Category') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->category?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Amount') }}</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $expense->formatted_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Payment Method') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->transaction_method_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Paid To') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->paid_to ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $expense->description ?? '-' }}</dd>
                    </div>
                </dl>

                @if($expense->attachment_path)
                    <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attachment') }}</dt>
                        <dd class="mt-1">
                            <a href="{{ Storage::url($expense->attachment_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <iconify-icon icon="lucide:paperclip" class="mr-1 h-4 w-4"></iconify-icon>
                                {{ __('View Attachment') }}
                            </a>
                        </dd>
                    </div>
                @endif
            </x-card>

            <!-- Audit Trail -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('History') }}</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                            <iconify-icon icon="lucide:plus" class="h-4 w-4"></iconify-icon>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('Created by') }} {{ $expense->createdBy?->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </li>
                    @if($expense->approved_at)
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $expense->isRejected() ? 'bg-red-100 text-red-600 dark:bg-red-900/30' : 'bg-green-100 text-green-600 dark:bg-green-900/30' }}">
                                <iconify-icon icon="lucide:{{ $expense->isRejected() ? 'x' : 'check' }}" class="h-4 w-4"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $expense->isRejected() ? __('Rejected by') : __('Approved by') }} {{ $expense->approvedBy?->name ?? 'System' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->approved_at->format('M d, Y H:i') }}</p>
                            </div>
                        </li>
                    @endif
                    @if($expense->paid_at)
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30">
                                <iconify-icon icon="lucide:banknote" class="h-4 w-4"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ __('Marked as Paid') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $expense->paid_at->format('M d, Y H:i') }}</p>
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
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold bg-{{ $expense->status_color }}-100 text-{{ $expense->status_color }}-800 dark:bg-{{ $expense->status_color }}-900/30 dark:text-{{ $expense->status_color }}-400">
                        {{ $expense->status_label }}
                    </span>
                </div>

                @if($expense->isRejected() && $expense->rejection_reason)
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                        <p class="text-sm font-medium text-red-800 dark:text-red-400">{{ __('Rejection Reason') }}</p>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $expense->rejection_reason }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Actions Card -->
            <x-card class="bg-white dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Actions') }}</h3>
                <div class="space-y-3">
                    @if($expense->canBeEdited())
                        <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-secondary w-full">
                            <iconify-icon icon="lucide:edit" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Edit') }}
                        </a>
                    @endif

                    @if($expense->isDraft())
                        <form action="{{ route('admin.expenses.submit-for-approval', $expense) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full">
                                <iconify-icon icon="lucide:send" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Submit for Approval') }}
                            </button>
                        </form>
                    @endif

                    @if($expense->canBeApproved())
                        <form action="{{ route('admin.expenses.approve', $expense) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-full">
                                <iconify-icon icon="lucide:check" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Approve') }}
                            </button>
                        </form>

                        <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="btn btn-danger w-full">
                            <iconify-icon icon="lucide:x" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Reject') }}
                        </button>
                    @endif

                    @if($expense->canBePaid())
                        <form action="{{ route('admin.expenses.mark-as-paid', $expense) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-full">
                                <iconify-icon icon="lucide:banknote" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Mark as Paid') }}
                            </button>
                        </form>
                    @endif

                    @if($expense->canBeEdited())
                        <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this expense?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-full">
                                <iconify-icon icon="lucide:trash-2" class="mr-2 h-4 w-4"></iconify-icon>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary w-full">
                        <iconify-icon icon="lucide:arrow-left" class="mr-2 h-4 w-4"></iconify-icon>
                        {{ __('Back to List') }}
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
            <div class="relative w-full max-w-lg transform rounded-lg bg-white p-6 shadow-xl transition-all dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Reject Expense') }}</h3>
                <form action="{{ route('admin.expenses.reject', $expense) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Reason for Rejection') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Reject Expense') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
