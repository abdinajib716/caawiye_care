@php
    // Read fresh values directly from .env file to prevent caching
    $envPath = base_path('.env');
    $envContent = file_get_contents($envPath);
    
    // Helper function to parse .env values
    $parseEnvValue = function($key) use ($envContent) {
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            $value = trim($matches[1]);
            // Remove surrounding quotes
            $value = trim($value, '"\'');
            return $value;
        }
        return '';
    };

    // Get current values from .env file
    $currentMailer = $parseEnvValue('MAIL_MAILER') ?: 'smtp';
    $currentHost = $parseEnvValue('MAIL_HOST');
    $currentPort = $parseEnvValue('MAIL_PORT') ?: '587';
    $currentUsername = $parseEnvValue('MAIL_USERNAME');
    $currentPassword = $parseEnvValue('MAIL_PASSWORD');
    $currentEncryption = $parseEnvValue('MAIL_ENCRYPTION') ?: 'tls';
    $currentFromAddress = $parseEnvValue('MAIL_FROM_ADDRESS');
    $currentFromName = $parseEnvValue('MAIL_FROM_NAME') ?: config('app.name', 'Laravel');
@endphp

{!! Hook::applyFilters(SettingFilterHook::SETTINGS_EMAIL_TAB_BEFORE_SECTION_START->value, '') !!}
<div class="rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 sm:py-5">
        <h3 class="text-base font-medium text-gray-700 dark:text-white/90">
            {{ __('Email Configuration') }}
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Configure email settings for your application') }}
        </p>
    </div>
    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
        
        {{-- Mail Driver --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Driver') }}
                </label>
                <select name="mail_mailer" class="form-control">
                    <option value="smtp" {{ $currentMailer === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ $currentMailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    <option value="mailgun" {{ $currentMailer === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                    <option value="ses" {{ $currentMailer === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    <option value="log" {{ $currentMailer === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Select the mail service to use for sending emails') }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Host') }}
                </label>
                <input type="text" name="mail_host" placeholder="{{ __('smtp.mailtrap.io') }}"
                    value="{{ $currentHost }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('SMTP server hostname') }}
                </p>
            </div>
        </div>

        {{-- Mail Port and Encryption --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Port') }}
                </label>
                <input type="number" name="mail_port" placeholder="587"
                    value="{{ $currentPort }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('SMTP server port (587 for TLS, 465 for SSL, 25 for no encryption)') }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Encryption') }}
                </label>
                <select name="mail_encryption" class="form-control">
                    <option value="" {{ $currentEncryption === null || $currentEncryption === '' ? 'selected' : '' }}>{{ __('None') }}</option>
                    <option value="tls" {{ $currentEncryption === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ $currentEncryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Encryption method for secure email transmission') }}
                </p>
            </div>
        </div>

        {{-- Mail Credentials --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Username') }}
                </label>
                <input type="text" name="mail_username" placeholder="{{ __('your-username') }}"
                    value="{{ $currentUsername }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('SMTP authentication username') }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Mail Password') }}
                </label>
                <input type="password" name="mail_password" placeholder="{{ __('your-password') }}"
                    value="{{ $currentPassword }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('SMTP authentication password') }}
                </p>
            </div>
        </div>

        {{-- From Address and Name --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('From Email Address') }}
                </label>
                <input type="email" name="mail_from_address" placeholder="{{ __('noreply@caawiyecare.com') }}"
                    value="{{ $currentFromAddress }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Default sender email address for outgoing emails') }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('From Name') }}
                </label>
                <input type="text" name="mail_from_name" placeholder="{{ __('Caawiye Care') }}"
                    value="{{ $currentFromName }}"
                    class="form-control">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Default sender name for outgoing emails') }}
                </p>
            </div>
        </div>

        {{-- Test Email Section --}}
        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <iconify-icon icon="lucide:mail" class="h-5 w-5 text-blue-400"></iconify-icon>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('Test Email Configuration') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>{{ __('After saving your email settings, you can test the configuration by sending a test email.') }}</p>
                    </div>
                    <div class="mt-4">
                        <div class="flex space-x-2">
                            <input type="email" placeholder="{{ __('test@example.com') }}"
                                class="form-control flex-1" id="test-email-address">
                            <button type="button" class="btn-secondary" onclick="testSmtpConnection()" id="test-connection-btn">
                                {{ __('Test Connection') }}
                            </button>
                            <button type="button" class="btn-secondary" onclick="sendTestEmail()" id="send-test-btn">
                                {{ __('Send Test Email') }}
                            </button>
                        </div>

                        {{-- Test Results --}}
                        <div id="test-results" class="mt-4 hidden">
                            <div id="test-result-content" class="p-3 rounded-md border">
                                <!-- Test results will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Removed: Healthcare Email Templates Info - Still cleaning demo dashboard --}}

    </div>
</div>
{!! Hook::applyFilters(SettingFilterHook::SETTINGS_EMAIL_TAB_AFTER_SECTION_END->value, '') !!}

@push('scripts')
<script>
// Clean up URL parameters after successful save
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('t')) {
        // Remove timestamp parameter from URL for cleaner address bar
        urlParams.delete('t');
        const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', cleanUrl);
    }
});

// Test SMTP connection without sending email
async function testSmtpConnection() {
    const btn = document.getElementById('test-connection-btn');
    const originalText = btn.textContent;

    try {
        btn.disabled = true;
        btn.textContent = '{{ __("Testing...") }}';

        const response = await fetch('{{ route("admin.settings.test-smtp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        });

        const result = await response.json();

        showTestResult(result, 'connection');

        if (typeof window.showToast === 'function') {
            if (result.success) {
                window.showToast('success', '{{ __("Success") }}', result.message);
            } else {
                window.showToast('error', '{{ __("Error") }}', result.message);
            }
        }

    } catch (error) {
        console.error('Connection test failed:', error);
        if (typeof window.showToast === 'function') {
            window.showToast('error', '{{ __("Error") }}', '{{ __("Failed to test connection") }}');
        }
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

// Send test email
async function sendTestEmail() {
    const emailAddress = document.getElementById('test-email-address').value;
    const btn = document.getElementById('send-test-btn');
    const originalText = btn.textContent;

    if (!emailAddress) {
        if (typeof window.showToast === 'function') {
            window.showToast('warning', '{{ __("Warning") }}', '{{ __("Please enter an email address") }}');
        }
        return;
    }

    try {
        btn.disabled = true;
        btn.textContent = '{{ __("Sending...") }}';

        const response = await fetch('{{ route("admin.settings.send-test-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: emailAddress
            })
        });

        const result = await response.json();

        showTestResult(result, 'email');

        if (typeof window.showToast === 'function') {
            if (result.success) {
                window.showToast('success', '{{ __("Success") }}', result.message);
            } else {
                window.showToast('error', '{{ __("Error") }}', result.message);
            }
        }

    } catch (error) {
        console.error('Test email failed:', error);
        if (typeof window.showToast === 'function') {
            window.showToast('error', '{{ __("Error") }}', '{{ __("Failed to send test email") }}');
        }
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

// Show test results
function showTestResult(result, type) {
    const resultsDiv = document.getElementById('test-results');
    const contentDiv = document.getElementById('test-result-content');

    let html = '';
    let bgClass = result.success ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800';
    let textClass = result.success ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200';
    let icon = result.success ? 'lucide:check-circle' : 'lucide:x-circle';

    html += `<div class="${bgClass} ${textClass}">`;
    html += `<div class="flex items-start">`;
    html += `<iconify-icon icon="${icon}" class="h-5 w-5 mt-0.5 mr-3"></iconify-icon>`;
    html += `<div class="flex-1">`;
    html += `<h4 class="font-medium">${type === 'connection' ? '{{ __("Connection Test") }}' : '{{ __("Email Test") }}'}</h4>`;
    html += `<p class="mt-1 text-sm">${result.message}</p>`;

    if (result.data && result.data.config_used) {
        html += `<div class="mt-2 text-xs">`;
        html += `<strong>{{ __("Configuration:") }}</strong> `;
        html += `Host: ${result.data.config_used.host || 'N/A'}, `;
        html += `Port: ${result.data.config_used.port || 'N/A'}, `;
        html += `Encryption: ${result.data.config_used.encryption || 'None'}`;
        html += `</div>`;
    }

    if (result.error) {
        html += `<div class="mt-2 text-xs font-mono bg-black/10 dark:bg-white/10 p-2 rounded">${result.error}</div>`;
    }

    html += `</div></div></div>`;

    contentDiv.innerHTML = html;
    contentDiv.className = `p-3 rounded-md border ${bgClass}`;
    resultsDiv.classList.remove('hidden');
}
</script>
@endpush
