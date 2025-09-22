<?php

declare(strict_types=1);

namespace App\Services;

use App\Concerns\HasActionLogTrait;
use App\Enums\ActionType;
use Exception;

class EmailConfigurationService
{
    use HasActionLogTrait;

    public function __construct(
        private EnvWriter $envWriter
    ) {}

    /**
     * Get predefined email configurations for common providers
     */
    public function getEmailProviderConfigurations(): array
    {
        return [
            'mailtrap' => [
                'name' => 'Mailtrap (Testing)',
                'config' => [
                    'mail_mailer' => 'smtp',
                    'mail_host' => 'smtp.mailtrap.io',
                    'mail_port' => '2525',
                    'mail_encryption' => null,
                    'mail_username' => '', // User needs to fill
                    'mail_password' => '', // User needs to fill
                    'mail_from_address' => 'noreply@caawiyecare.com',
                    'mail_from_name' => 'Caawiye Care',
                ],
                'instructions' => 'Sign up at mailtrap.io, create an inbox, and use the SMTP credentials provided.',
            ],
            'gmail' => [
                'name' => 'Gmail SMTP',
                'config' => [
                    'mail_mailer' => 'smtp',
                    'mail_host' => 'smtp.gmail.com',
                    'mail_port' => '587',
                    'mail_encryption' => 'tls',
                    'mail_username' => '', // User needs to fill
                    'mail_password' => '', // User needs to fill (App Password)
                    'mail_from_address' => '', // User needs to fill
                    'mail_from_name' => 'Caawiye Care',
                ],
                'instructions' => 'Enable 2FA on Gmail, generate App Password, use your Gmail address and the 16-character App Password.',
            ],
            'sendgrid' => [
                'name' => 'SendGrid',
                'config' => [
                    'mail_mailer' => 'smtp',
                    'mail_host' => 'smtp.sendgrid.net',
                    'mail_port' => '587',
                    'mail_encryption' => 'tls',
                    'mail_username' => 'apikey',
                    'mail_password' => '', // User needs to fill (API Key)
                    'mail_from_address' => '', // User needs to fill (verified sender)
                    'mail_from_name' => 'Caawiye Care',
                ],
                'instructions' => 'Create SendGrid account, generate API key, verify sender email address.',
            ],
            'mailgun' => [
                'name' => 'Mailgun',
                'config' => [
                    'mail_mailer' => 'smtp',
                    'mail_host' => 'smtp.mailgun.org',
                    'mail_port' => '587',
                    'mail_encryption' => 'tls',
                    'mail_username' => '', // User needs to fill (SMTP username)
                    'mail_password' => '', // User needs to fill (SMTP password)
                    'mail_from_address' => '', // User needs to fill (verified domain)
                    'mail_from_name' => 'Caawiye Care',
                ],
                'instructions' => 'Create Mailgun account, add and verify domain, use SMTP credentials from domain settings.',
            ],
        ];
    }

    /**
     * Apply a predefined configuration
     */
    public function applyProviderConfiguration(string $provider, array $userInputs = []): array
    {
        try {
            $providers = $this->getEmailProviderConfigurations();
            
            if (!isset($providers[$provider])) {
                throw new Exception("Unknown email provider: {$provider}");
            }

            $config = $providers[$provider]['config'];
            
            // Merge user inputs
            foreach ($userInputs as $key => $value) {
                if (array_key_exists($key, $config)) {
                    $config[$key] = $value;
                }
            }

            // Write to .env file
            $this->envWriter->batchWriteKeysToEnvFile($config);

            // Log the action
            $this->storeActionLog(ActionType::UPDATED, [
                'email_provider' => $provider,
                'config_applied' => array_keys($config)
            ], "Email configuration updated to use {$providers[$provider]['name']}");

            return [
                'success' => true,
                'message' => "Successfully configured email for {$providers[$provider]['name']}",
                'provider' => $provider,
                'config' => $config
            ];

        } catch (Exception $e) {
            $this->storeActionLog(ActionType::EXCEPTION, [
                'error' => $e->getMessage(),
                'provider' => $provider
            ]);

            return [
                'success' => false,
                'message' => 'Failed to apply email configuration: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate email configuration completeness
     */
    public function validateConfiguration(array $config): array
    {
        $issues = [];
        $warnings = [];

        // Required fields
        $required = ['mail_mailer', 'mail_host', 'mail_port'];
        foreach ($required as $field) {
            if (empty($config[$field])) {
                $issues[] = "Missing required field: {$field}";
            }
        }

        // Validate port
        if (!empty($config['mail_port']) && (!is_numeric($config['mail_port']) || $config['mail_port'] < 1 || $config['mail_port'] > 65535)) {
            $issues[] = 'Invalid port number. Must be between 1 and 65535.';
        }

        // Validate from_address
        if (!empty($config['mail_from_address']) && !filter_var($config['mail_from_address'], FILTER_VALIDATE_EMAIL)) {
            $issues[] = 'Invalid from email address format.';
        }

        // Validate encryption
        if (!empty($config['mail_encryption']) && !in_array($config['mail_encryption'], ['tls', 'ssl'])) {
            $issues[] = 'Invalid encryption method. Must be "tls" or "ssl".';
        }

        // Warnings for missing optional but recommended fields
        if (empty($config['mail_from_address'])) {
            $warnings[] = 'From email address not set. Emails may not be delivered properly.';
        }

        if (empty($config['mail_from_name'])) {
            $warnings[] = 'From name not set. Emails will use default application name.';
        }

        // Gmail specific validation
        if ($config['mail_host'] === 'smtp.gmail.com') {
            if (empty($config['mail_username']) || !str_contains($config['mail_username'], '@gmail.com')) {
                $issues[] = 'Gmail configuration requires a valid Gmail address as username.';
            }
            
            if (empty($config['mail_password']) || strlen($config['mail_password']) !== 16) {
                $warnings[] = 'Gmail requires a 16-character App Password. Make sure 2FA is enabled and you\'ve generated an App Password.';
            }
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
            'score' => $this->calculateConfigurationScore($config, $issues, $warnings)
        ];
    }

    /**
     * Calculate configuration completeness score
     */
    private function calculateConfigurationScore(array $config, array $issues, array $warnings): int
    {
        $score = 100;
        
        // Deduct points for issues
        $score -= count($issues) * 25;
        
        // Deduct points for warnings
        $score -= count($warnings) * 10;
        
        // Bonus points for complete configuration
        $optionalFields = ['mail_username', 'mail_password', 'mail_from_address', 'mail_from_name', 'mail_encryption'];
        $completedOptional = 0;
        
        foreach ($optionalFields as $field) {
            if (!empty($config[$field])) {
                $completedOptional++;
            }
        }
        
        $score += ($completedOptional / count($optionalFields)) * 20;
        
        return max(0, min(100, (int)$score));
    }

    /**
     * Get current configuration status with recommendations
     */
    public function getConfigurationStatus(): array
    {
        $emailService = app(EmailService::class);
        $currentConfig = $emailService->getCurrentEmailConfig();
        $validation = $this->validateConfiguration($currentConfig);
        
        // Detect provider
        $detectedProvider = $this->detectProvider($currentConfig);
        
        return [
            'current_config' => $currentConfig,
            'detected_provider' => $detectedProvider,
            'validation' => $validation,
            'available_providers' => array_keys($this->getEmailProviderConfigurations()),
            'recommendations' => $this->getRecommendations($currentConfig, $validation)
        ];
    }

    /**
     * Detect which provider is being used
     */
    private function detectProvider(array $config): ?string
    {
        $providers = $this->getEmailProviderConfigurations();
        
        foreach ($providers as $key => $provider) {
            if (isset($config['host']) && $config['host'] === $provider['config']['mail_host']) {
                return $key;
            }
        }
        
        return null;
    }

    /**
     * Get configuration recommendations
     */
    private function getRecommendations(array $config, array $validation): array
    {
        $recommendations = [];
        
        if (!$validation['valid']) {
            $recommendations[] = 'Fix configuration issues before testing email functionality.';
        }
        
        if ($validation['score'] < 80) {
            $recommendations[] = 'Complete optional fields for better email delivery.';
        }
        
        if (empty($config['username']) && empty($config['password'])) {
            $recommendations[] = 'Consider using Mailtrap for testing or configure proper SMTP credentials.';
        }
        
        return $recommendations;
    }
}
