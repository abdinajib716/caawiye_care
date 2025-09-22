<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Enums\Hooks\SettingFilterHook;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CacheService;
use App\Services\EmailService;
use App\Services\EnvWriter;
use App\Services\ImageService;
use App\Services\RecaptchaService;
use App\Services\SettingService;
use App\Support\Facades\Hook;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly EnvWriter $envWriter,
        private readonly CacheService $cacheService,
        private readonly ImageService $imageService,
        private readonly RecaptchaService $recaptchaService,
        private readonly EmailService $emailService,
    ) {
    }

    public function index($tab = null)
    {
        $this->authorize('manage', Setting::class);

        $tab = $tab ?? request()->input('tab', 'general');

        // Prepare breadcrumbs data
        $breadcrumbs = [
            'title' => __('Settings'),
        ];

        // Add cache-busting headers to prevent form caching
        return response()
            ->view('backend.pages.settings.index', compact('tab', 'breadcrumbs'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $this->authorize('manage', Setting::class);

        // Restrict specific fields in demo mode.
        if (config('app.demo_mode', false)) {
            $restrictedFields = Hook::applyFilters(SettingFilterHook::SETTINGS_RESTRICTED_FIELDS, [
                'app_name',
                'google_analytics_script',
                'recaptcha_site_key',
                'recaptcha_secret_key',
                'recaptcha_enabled_pages',
                'recaptcha_score_threshold',
                'admin_login_route',
                'disable_default_admin_redirect',
            ]);
            $fields = $request->except($restrictedFields);
        } else {
            $fields = $request->all();
        }

        // Validate admin login route if provided
        if ($request->has('admin_login_route')) {
            $request->validate([
                'admin_login_route' => 'required|regex:/^[a-zA-Z0-9\-\_\/]+$/|min:3|max:50',
            ], [
                'admin_login_route.regex' => 'The admin login route can only contain letters, numbers, hyphens, underscores and forward slashes.',
            ]);
        }

        $uploadPath = 'uploads/settings';

        // Handle checkbox fields that might not be present when unchecked
        $checkboxFields = ['disable_default_admin_redirect'];
        foreach ($checkboxFields as $checkboxField) {
            // Skip restricted fields in demo mode
            if (config('app.demo_mode', false) && in_array($checkboxField, $restrictedFields ?? [])) {
                continue;
            }

            if (! isset($fields[$checkboxField]) && $request->has('_token')) {
                // If the form was submitted but checkbox wasn't checked, set to 0
                $fields[$checkboxField] = '0';
            }
        }

        // Email configuration fields that should only be written to .env (not database)
        $emailEnvFields = [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ];

        foreach ($fields as $fieldName => $fieldValue) {
            if ($request->hasFile($fieldName)) {
                $this->imageService->deleteImageFromPublic((string) config($fieldName));
                $fileUrl = $this->imageService->storeImageAndGetUrl($request, $fieldName, $uploadPath);
                $this->settingService->addSetting($fieldName, $fileUrl);
            } elseif ($fieldName === 'recaptcha_enabled_pages') {
                // Validate enabled pages against allowed list.
                $enabledPages = $request->input('recaptcha_enabled_pages', []);
                $validPages = array_keys($this->recaptchaService::getAvailablePages());
                $enabledPages = array_intersect($enabledPages, $validPages);
                $this->settingService->addSetting($fieldName, json_encode(array_values($enabledPages)));
            } elseif (!in_array($fieldName, $emailEnvFields)) {
                // Only save non-email fields to database
                $this->settingService->addSetting($fieldName, $fieldValue);
            }
            // Email fields will be handled by batchWriteKeysToEnvFile below
        }

        $this->envWriter->batchWriteKeysToEnvFile($fields);

        // Clear ALL caches to ensure updated values are reflected immediately
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');
        \Artisan::call('cache:clear');

        // Force refresh of environment variables (production servers)
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        $this->storeActionLog(ActionType::UPDATED, [
            'settings' => $fields,
        ]);

        // CRITICAL: Clear session flash data to prevent old() helper interference
        session()->forget(['_old_input', '_flash']);

        // Use PRG pattern (Post-Redirect-Get) with explicit tab parameter
        return redirect()->route('admin.settings.index', ['tab' => 'email'])
            ->with('success', 'Settings saved successfully!')
            ->with('settings_updated', true);
    }

    /**
     * Test SMTP connection
     */
    public function testSmtpConnection(Request $request)
    {
        $this->authorize('manage', Setting::class);

        try {
            $result = $this->emailService->testSmtpConnection();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $this->authorize('manage', Setting::class);

        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $result = $this->emailService->sendTestEmail($request->input('email'));

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test email failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
