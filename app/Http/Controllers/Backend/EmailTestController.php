<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class EmailTestController extends Controller
{
    public function __construct(
        private EmailService $emailService
    ) {}

    /**
     * Send a test email
     */
    public function sendTestEmail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'test_config' => 'sometimes|array',
                'test_config.mail_host' => 'sometimes|string|max:255',
                'test_config.mail_port' => 'sometimes|integer|min:1|max:65535',
                'test_config.mail_username' => 'sometimes|string|max:255',
                'test_config.mail_password' => 'sometimes|string|max:255',
                'test_config.mail_encryption' => 'sometimes|in:tls,ssl',
                'test_config.mail_from_address' => 'sometimes|email|max:255',
                'test_config.mail_from_name' => 'sometimes|string|max:255',
            ]);

            $result = $this->emailService->sendTestEmail(
                $validated['email'],
                $validated['test_config'] ?? []
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'recipient' => $validated['email'],
                        'config_used' => $result['config_used'] ?? null,
                        'sent_at' => now()->toISOString()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error' => $result['error'] ?? null
                ], 400);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SMTP connection without sending email
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'test_config' => 'sometimes|array',
                'test_config.mail_host' => 'sometimes|required|string|max:255',
                'test_config.mail_port' => 'sometimes|required|integer|min:1|max:65535',
            ]);

            $result = $this->emailService->testSmtpConnection(
                $validated['test_config'] ?? []
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['config_tested'] ?? null,
                'error' => $result['error'] ?? null
            ], $result['success'] ? 200 : 400);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while testing the connection',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current email configuration status
     */
    public function getConfigurationStatus(): JsonResponse
    {
        try {
            $status = $this->emailService->getConfigurationStatus();

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get configuration status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current email configuration (sanitized)
     */
    public function getCurrentConfig(): JsonResponse
    {
        try {
            $config = $this->emailService->getCurrentEmailConfig();
            
            // Remove sensitive data
            if (isset($config['password'])) {
                $config['password'] = $config['password'] ? '***SET***' : null;
            }

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
