<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white/90">
                {{ __('Create Expense Category') }}
            </h2>
        </div>
        <x-messages />
    </x-slot>

    <x-card class="bg-white dark:bg-gray-800">
        <div class="p-6">
            <form action="{{ route('admin.expense-categories.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="form-label">{{ __('Slug') }}</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-control @error('slug') border-red-500 @enderror" placeholder="{{ __('Auto-generated if empty') }}">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea id="description" name="description" rows="3" class="form-control @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('Status') }}</label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Category') }}
                    </button>
                </div>
            </form>
        </div>
    </x-card>
</x-layouts.backend-layout>
