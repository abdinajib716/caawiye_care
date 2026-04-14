<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WaafipayService
{
    /**
     * Get WaafiPay configuration from settings.
     */
    protected function getConfig(): array
    {
        return [
            'enabled' => config('settings.waafipay_enabled', false),
            'environment' => config('settings.waafipay_environment', 'test'),
            'merchant_uid' => config('settings.waafipay_merchant_uid', ''),
            'api_user_id' => config('settings.waafipay_api_user_id', ''),
            'api_key' => config('settings.waafipay_api_key', ''),
            'merchant_no' => config('settings.waafipay_merchant_no', ''),
            'api_url' => config('settings.waafipay_api_url', 'https://api.waafipay.net/asm'),
        ];
    }

    /**
     * Check if WaafiPay is enabled.
     */
    public function isEnabled(): bool
    {
        $config = $this->getConfig();
        return (bool) $config['enabled'];
    }

    /**
     * Validate WaafiPay credentials.
     */
    public function validateCredentials(): bool
    {
        $config = $this->getConfig();
        
        return !empty($config['merchant_uid']) &&
               !empty($config['api_user_id']) &&
               !empty($config['api_key']) &&
               !empty($config['merchant_no']) &&
               !empty($config['api_url']);
    }

    /**
     * Format phone number to international format (252XXXXXXXXX).
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove any spaces, dashes, or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If phone starts with 252, return as is
        if (str_starts_with($phone, '252')) {
            return $phone;
        }
        
        // If phone starts with +252, remove the +
        if (str_starts_with($phone, '+252')) {
            return substr($phone, 1);
        }
        
        // If phone starts with 0, remove it and add 252
        if (str_starts_with($phone, '0')) {
            return '252' . substr($phone, 1);
        }
        
        // Otherwise, add 252 prefix
        return '252' . $phone;
    }

    /**
     * Validate Somalia phone number.
     */
    public function validatePhoneNumber(string $phone): bool
    {
        $formattedPhone = $this->formatPhoneNumber($phone);
        
        // Check if it's a valid Somalia number (252 + 9 digits)
        if (!preg_match('/^252[0-9]{9}$/', $formattedPhone)) {
            return false;
        }
        
        // Extract the prefix (first 2 digits after 252)
        $prefix = substr($formattedPhone, 3, 2);
        
        // Valid Somalia mobile prefixes
        $validPrefixes = ['61', '77', '68', '63', '90'];
        
        return in_array($prefix, $validPrefixes);
    }

    /**
     * Get provider name from phone number.
     */
    public function getProviderFromPhone(string $phone): ?string
    {
        $formattedPhone = $this->formatPhoneNumber($phone);
        $prefix = substr($formattedPhone, 3, 2);
        
        return match ($prefix) {
            '61', '77' => 'EVC PLUS',
            '68' => 'JEEB',
            '63' => 'ZAAD',
            '90' => 'SAHAL',
            default => null,
        };
    }

    /**
     * Generate unique reference ID.
     */
    public function generateReferenceId(): string
    {
        return app(\App\Services\SequentialIdService::class)->generateTransactionReference();
    }

    /**
     * Build WaafiPay API payload.
     */
    protected function buildPayload(array $data): array
    {
        $config = $this->getConfig();
        
        return [
            'schemaVersion' => '1.0',
            'requestId' => $data['reference_id'],
            'timestamp' => now()->toIso8601String(),
            'channelName' => 'WEB',
            'serviceName' => 'API_PURCHASE',
            'serviceParams' => [
                'merchantUid' => $config['merchant_uid'],
                'apiUserId' => $config['api_user_id'],
                'apiKey' => $config['api_key'],
                'paymentMethod' => 'MWALLET_ACCOUNT',
                'payerInfo' => [
                    'accountNo' => $data['phone'],
                ],
                'transactionInfo' => [
                    'referenceId' => $data['reference_id'],
                    'invoiceId' => $data['invoice_id'] ?? $data['reference_id'],
                    'amount' => (float) $data['amount'],
                    'currency' => $data['currency'] ?? 'USD',
                    'description' => $data['description'] ?? 'Payment',
                ],
            ],
        ];
    }

    /**
     * Process payment through WaafiPay API.
     */
    public function processPayment(array $data): array
    {
        try {
            // Validate credentials
            if (!$this->validateCredentials()) {
                return [
                    'success' => false,
                    'message' => 'WaafiPay credentials are not configured properly.',
                ];
            }

            // Format phone number
            $data['phone'] = $this->formatPhoneNumber($data['phone']);

            // Validate phone number
            if (!$this->validatePhoneNumber($data['phone'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid Somalia mobile number.',
                ];
            }

            // Generate reference ID if not provided
            if (empty($data['reference_id'])) {
                $data['reference_id'] = $this->generateReferenceId();
            }

            // Get provider from phone
            $provider = $this->getProviderFromPhone($data['phone']);

            // Create payment transaction record
            $transaction = PaymentTransaction::create([
                'reference_id' => $data['reference_id'],
                'invoice_id' => $data['invoice_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'payment_method' => 'MWALLET_ACCOUNT',
                'provider' => $provider,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['phone'],
                'customer_id' => $data['customer_id'] ?? null,
                'status' => 'pending',
                'description' => $data['description'] ?? 'Payment',
            ]);

            // Build API payload
            $payload = $this->buildPayload($data);

            // Store request payload
            $transaction->update(['request_payload' => $payload]);

            // Send request to WaafiPay API
            $config = $this->getConfig();
            $response = Http::timeout(90)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($config['api_url'], $payload);

            // Log the response
            Log::info('WaafiPay API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            // Update transaction with response
            $responseData = $response->json();
            $transaction->update([
                'response_data' => $responseData,
                'response_code' => $responseData['responseCode'] ?? null,
                'response_message' => $responseData['responseMsg'] ?? null,
            ]);

            // Check response
            if ($response->successful() && isset($responseData['responseCode'])) {
                if ($responseData['responseCode'] === '2001') {
                    // Payment successful
                    $transaction->markAsCompleted($responseData);
                    
                    return [
                        'success' => true,
                        'message' => 'Payment completed successfully.',
                        'transaction' => $transaction,
                        'response' => $responseData,
                    ];
                } elseif ($responseData['responseCode'] === '5310') {
                    // Payment rejected or cancelled by user - Mark as FAILED
                    $errorMessage = $responseData['responseMsg'] ?? 'Payment rejected by user';
                    $transaction->markAsFailed($errorMessage, $responseData);
                    
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'transaction' => $transaction,
                        'response' => $responseData,
                    ];
                } else {
                    // Payment failed
                    $errorMessage = $responseData['responseMsg'] ?? 'Payment failed';
                    $transaction->markAsFailed($errorMessage, $responseData);
                    
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'transaction' => $transaction,
                        'response' => $responseData,
                    ];
                }
            }

            // API request failed
            $transaction->markAsFailed('API request failed', $responseData ?? []);
            
            return [
                'success' => false,
                'message' => 'Failed to connect to WaafiPay API.',
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            Log::error('WaafiPay Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($transaction)) {
                $transaction->markAsFailed($e->getMessage());
            }

            return [
                'success' => false,
                'message' => 'An error occurred while processing payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check payment status.
     */
    public function checkPaymentStatus(string $referenceId): array
    {
        $transaction = PaymentTransaction::where('reference_id', $referenceId)->first();

        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Transaction not found.',
            ];
        }

        return [
            'success' => true,
            'transaction' => $transaction,
            'status' => $transaction->status,
        ];
    }
}

