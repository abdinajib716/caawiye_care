<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="mx-auto max-w-2xl">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Edit Supplier') }}</h3>
            </x-slot>

            <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="name" class="form-label">{{ __('Supplier Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') border-red-500 @enderror" value="{{ old('name', $supplier->name) }}" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="form-label">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') border-red-500 @enderror" value="{{ old('phone', $supplier->phone) }}" required>
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') border-red-500 @enderror" value="{{ old('email', $supplier->email) }}">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="address" class="form-label">{{ __('Address') }}</label>
                        <textarea name="address" id="address" rows="3" class="form-control @error('address') border-red-500 @enderror">{{ old('address', $supplier->address) }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="form-label">{{ __('Status') }} <span class="text-red-500">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', $supplier->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ old('status', $supplier->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="lucide:save" class="mr-2 h-4 w-4"></iconify-icon>
                            {{ __('Update Supplier') }}
                        </button>
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.backend-layout>
