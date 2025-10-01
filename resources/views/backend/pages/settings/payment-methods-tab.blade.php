<div class="rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 sm:py-5">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/waafi/waafipay logo.jpg') }}" alt="WaafiPay Logo" class="h-10 w-auto">
            <div>
                <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
                    {{ __('WaafiPay Configuration') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Configure WaafiPay payment gateway for Somalia mobile money payments') }}
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
        {{-- Enable WaafiPay --}}
        <div class="flex items-center">
            <input
                type="checkbox"
                name="waafipay_enabled"
                id="waafipay_enabled"
                value="1"
                {{ config('settings.waafipay_enabled') ? 'checked' : '' }}
                class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary dark:focus:ring-primary dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            >
            <label for="waafipay_enabled" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                {{ __('Enable WaafiPay') }}
            </label>
        </div>

        {{-- Environment and Credentials --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="waafipay_environment" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Select Environment') }}
                </label>
                <select
                    name="waafipay_environment"
                    id="waafipay_environment"
                    class="form-control"
                >
                    <option value="test" {{ config('settings.waafipay_environment') === 'test' ? 'selected' : '' }}>{{ __('TEST') }}</option>
                    <option value="live" {{ config('settings.waafipay_environment') === 'live' ? 'selected' : '' }}>{{ __('LIVE') }}</option>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Select TEST for testing or LIVE for production') }}
                </p>
            </div>

            <div>
                <label for="waafipay_merchant_uid" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('MERCHANT_U_ID') }}
                </label>
                <input
                    type="text"
                    name="waafipay_merchant_uid"
                    id="waafipay_merchant_uid"
                    value="{{ config('settings.waafipay_merchant_uid') }}"
                    placeholder="M1234567"
                    class="form-control"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Enter exactly as provided by WaafiPay (M + 7 digits)') }}
                </p>
            </div>
        </div>

        {{-- API User ID and Merchant No --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="waafipay_api_user_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('API_USER_ID') }}
                </label>
                <input
                    type="text"
                    name="waafipay_api_user_id"
                    id="waafipay_api_user_id"
                    value="{{ config('settings.waafipay_api_user_id') }}"
                    placeholder="1234567"
                    class="form-control"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Enter exactly as provided by WaafiPay (7 digits)') }}
                </p>
            </div>

            <div>
                <label for="waafipay_merchant_no" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('MERCHANT_NO') }}
                </label>
                <input
                    type="text"
                    name="waafipay_merchant_no"
                    id="waafipay_merchant_no"
                    value="{{ config('settings.waafipay_merchant_no') }}"
                    placeholder="123456789"
                    class="form-control"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Enter exactly as provided by WaafiPay (9 digits)') }}
                </p>
            </div>
        </div>

        {{-- API Key --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="waafipay_api_key" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('API_KEY') }}
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="waafipay_api_key"
                        id="waafipay_api_key"
                        value="{{ config('settings.waafipay_api_key') }}"
                        placeholder="API-123456789ABC"
                        class="form-control pr-10"
                    >
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('waafipay_api_key')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <iconify-icon icon="lucide:eye" class="w-5 h-5" id="waafipay_api_key_icon"></iconify-icon>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Enter exactly as provided by WaafiPay') }}
                </p>
            </div>

            <div>
                <label for="waafipay_api_url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('API URL') }}
                </label>
                <input
                    type="url"
                    name="waafipay_api_url"
                    id="waafipay_api_url"
                    value="{{ config('settings.waafipay_api_url', 'https://api.waafipay.net/asm') }}"
                    placeholder="https://api.waafipay.net/asm"
                    class="form-control"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('WaafiPay API endpoint URL') }}
                </p>
            </div>
        </div>

        {{-- Test WaafiPay Section --}}
        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <iconify-icon icon="lucide:test-tube" class="h-5 w-5 text-blue-400"></iconify-icon>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('Test WaafiPay Configuration') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>{{ __('After saving your WaafiPay settings, you can test the configuration by sending a test payment.') }}</p>
                    </div>
                    <div class="mt-4">
                        <button
                            type="button"
                            onclick="openTestWaafiPayModal()"
                            class="btn-secondary"
                        >
                            <iconify-icon icon="lucide:test-tube" class="w-4 h-4 mr-2"></iconify-icon>
                            {{ __('Test WaafiPay') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Credentials Format Info Box --}}
        <div class="rounded-md border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 p-4">
            <div class="flex items-start">
                <iconify-icon icon="lucide:info" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0"></iconify-icon>
                <div class="text-sm text-blue-800 dark:text-blue-300">
                    <p class="font-semibold mb-2">{{ __('WaafiPay Credentials Format:') }}</p>
                    <ul class="space-y-1 list-none font-mono text-xs">
                        <li><strong>MERCHANT_U_ID=</strong>M1234567</li>
                        <li><strong>API_USER_ID=</strong>1234567</li>
                        <li><strong>API_KEY=</strong>API-123456789ABC</li>
                        <li><strong>MERCHANT_NO=</strong>123456789</li>
                        <li><strong>URL=</strong>https://api.waafipay.net/asm</li>
                    </ul>
                    <p class="mt-2 italic">{{ __('Enter credentials exactly as provided by WaafiPay') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('icon', 'lucide:eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('icon', 'lucide:eye');
    }
}

function openTestWaafiPayModal() {
    Livewire.dispatch('openTestWaafiPayModal');
}
</script>
@endpush
