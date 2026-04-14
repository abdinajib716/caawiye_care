<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Create Expense') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <x-card class="bg-white dark:bg-gray-800">
            <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <x-inputs.date-picker 
                                name="expense_date" 
                                label="{{ __('Expense Date') }} *" 
                                :value="old('expense_date', date('Y-m-d'))"
                                required
                            />
                            @error('expense_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Category') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" class="form-control @error('category_id') border-red-500 @enderror" required>
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="amount" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Amount') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') border-red-500 @enderror" required>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="transaction_method" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Payment Method') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="transaction_method" name="transaction_method" class="form-control @error('transaction_method') border-red-500 @enderror" required>
                                <option value="cash" {{ old('transaction_method') == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                <option value="evc" {{ old('transaction_method') == 'evc' ? 'selected' : '' }}>{{ __('EVC Plus') }}</option>
                                <option value="edahab" {{ old('transaction_method') == 'edahab' ? 'selected' : '' }}>{{ __('E-Dahab') }}</option>
                                <option value="bank" {{ old('transaction_method') == 'bank' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                            </select>
                            @error('transaction_method')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="paid_to" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Paid To') }}
                        </label>
                        <input type="text" id="paid_to" name="paid_to" value="{{ old('paid_to') }}" class="form-control @error('paid_to') border-red-500 @enderror" placeholder="{{ __('Vendor, Employee, Provider name...') }}">
                        @error('paid_to')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Description') }}
                        </label>
                        <textarea id="description" name="description" rows="3" class="form-control @error('description') border-red-500 @enderror" placeholder="{{ __('Enter expense description...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="attachment" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Attachment (Receipt/Invoice)') }}
                        </label>
                        <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="form-control @error('attachment') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">{{ __('Accepted formats: PDF, JPG, PNG. Max size: 5MB') }}</p>
                        @error('attachment')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Expense') }}
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
