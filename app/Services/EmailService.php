<?php

declare(strict_types=1);

namespace App\Services;

use App\Concerns\HasActionLogTrait;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class EmailService
{
    use HasActionLogTrait;

    public function __construct(
        private Mailer $mailer
    ) {}

    /**
     * Test email configuration by sending a test email
     */
    public function sendTestEmail(string $toEmail, array $config = []): array
    {
        try {
            // Validate email address
            if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address provided');
            }

            // Get current email configuration
            $emailConfig = $this->getCurrentEmailConfig();
            
            // Override with provided config if any
            if (!empty($config)) {
                $emailConfig = array_merge($emailConfig, $config);
            }

            // Validate required configuration
            $this->validateEmailConfig($emailConfig);

            // Configure mail settings dynamically
            $this->configureMailSettings($emailConfig);

            // Send test email using Laravel's Mail facade
            Mail::raw(
                "Hello!\n\nThis is a test email from " . config('app.name') . ".\n\n" .
                "Test sent at: " . now()->format('Y-m-d H:i:s') . "\n" .
                "Recipient: " . $toEmail . "\n\n" .
                "If you received this email, your email configuration is working correctly!\n\n" .
                "Best regards,\n" . config('app.name') . " Team",
                function ($message) use ($toEmail) {
                    $message->to($toEmail)
                           ->subject('Test Email from ' . config('app.name'));
                }
            );

            // Log successful test
            $this->storeActionLog(\App\Enums\ActionType::UPDATED, [
                'action' => 'email_test_sent',
                'recipient' => $toEmail,
                'config' => $this->sanitizeConfigForLogging($emailConfig)
            ]);

            return [
                'success' => true,
                'message' => 'Test email sent successfully to ' . $toEmail,
                'config_used' => $this->sanitizeConfigForLogging($emailConfig)
            ];

        } catch (Exception $e) {
            // Log error
            Log::error('Email test failed', [
                'recipient' => $toEmail,
                'error' => $e->getMessage(),
                'config' => $this->sanitizeConfigForLogging($config)
            ]);

            $this->storeActionLog(\App\Enums\ActionType::UPDATED, [
                'action' => 'email_test_failed',
                'recipient' => $toEmail,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get current email configuration from environment and config
     */
    public function getCurrentEmailConfig(): array
    {
        return [
            'mailer' => config('settings.mail_mailer') ?? env('MAIL_MAILER', config('mail.default')),
            'host' => config('settings.mail_host') ?? env('MAIL_HOST', config('mail.mailers.smtp.host')),
            'port' => (int)(config('settings.mail_port') ?? env('MAIL_PORT', config('mail.mailers.smtp.port'))),
            'username' => config('settings.mail_username') ?? env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
            'password' => config('settings.mail_password') ?? env('MAIL_PASSWORD', config('mail.mailers.smtp.password')),
            'encryption' => config('settings.mail_encryption') ?? env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')),
            'from_address' => config('settings.mail_from_address') ?? env('MAIL_FROM_ADDRESS', config('mail.from.address')),
            'from_name' => config('settings.mail_from_name') ?? env('MAIL_FROM_NAME', config('mail.from.name')) ?? config('app.name'),
        ];
    }

    /**
     * Validate email configuration
     */
    public function validateEmailConfig(array $config): void
    {
        $required = ['mailer', 'host', 'port'];
        
        foreach ($required as $field) {
            if (empty($config[$field])) {
                throw new Exception("Email configuration missing required field: {$field}");
            }
        }

        // Validate port is numeric
        if (!is_numeric($config['port']) || $config['port'] < 1 || $config['port'] > 65535) {
            throw new Exception('Invalid port number. Must be between 1 and 65535.');
        }

        // Validate from_address if provided
        if (!empty($config['from_address']) && !filter_var($config['from_address'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid from email address');
        }

        // Validate encryption method
        if (!empty($config['encryption']) && !in_array($config['encryption'], ['tls', 'ssl'])) {
            throw new Exception('Invalid encryption method. Must be "tls" or "ssl"');
        }
    }

    /**
     * Configure mail settings dynamically for testing
     */
    private function configureMailSettings(array $config): void
    {
        // Set mail configuration dynamically
        config([
            'mail.default' => $config['mailer'],
            'mail.mailers.smtp.host' => $config['host'],
            'mail.mailers.smtp.port' => $config['port'],
            'mail.mailers.smtp.username' => $config['username'],
            'mail.mailers.smtp.password' => $config['password'],
            'mail.mailers.smtp.encryption' => $config['encryption'],
            'mail.from.address' => $config['from_address'] ?: 'noreply@' . request()->getHost(),
            'mail.from.name' => $config['from_name'] ?: config('app.name'),
        ]);

        // Purge the mailer instance to use new config
        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
    }

    /**
     * Sanitize config for logging (remove sensitive data)
     */
    private function sanitizeConfigForLogging(array $config): array
    {
        $sanitized = $config;
        if (isset($sanitized['password'])) {
            $sanitized['password'] = '***HIDDEN***';
        }
        return $sanitized;
    }

    /**
     * Test SMTP connection without sending email
     */
    public function testSmtpConnection(array $config = []): array
    {
        try {
            $emailConfig = $config ?: $this->getCurrentEmailConfig();
            $this->validateEmailConfig($emailConfig);

            // Test connection using socket
            $host = $emailConfig['host'];
            $port = $emailConfig['port'];
            $timeout = 10;

            $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
            
            if (!$socket) {
                throw new Exception("Cannot connect to {$host}:{$port} - {$errstr} ({$errno})");
            }

            fclose($socket);

            return [
                'success' => true,
                'message' => "Successfully connected to {$host}:{$port}",
                'config_tested' => $this->sanitizeConfigForLogging($emailConfig)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SMTP connection failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get email configuration status
     */
    public function getConfigurationStatus(): array
    {
        $config = $this->getCurrentEmailConfig();
        
        $status = [
            'configured' => false,
            'issues' => [],
            'config' => $this->sanitizeConfigForLogging($config)
        ];

        // Check required fields
        if (empty($config['host'])) {
            $status['issues'][] = 'SMTP host not configured';
        }
        
        if (empty($config['port'])) {
            $status['issues'][] = 'SMTP port not configured';
        }
        
        if (empty($config['from_address'])) {
            $status['issues'][] = 'From email address not configured';
        }

        // Check if basic configuration is complete
        $status['configured'] = empty($status['issues']);

        return $status;
    }
}
