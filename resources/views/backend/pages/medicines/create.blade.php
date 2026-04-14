<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-2xl">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Add New Medicine') }}</h3>
            </x-slot>

            <form action="{{ route('admin.medicines.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="name" class="form-label">{{ __('Medicine Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') border-red-500 @enderror" 
                               value="{{ old('name') }}"
                               placeholder="{{ __('Enter medicine name') }}"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Save Medicine') }}
                        </button>
                        <a href="{{ route('admin.medicines.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
