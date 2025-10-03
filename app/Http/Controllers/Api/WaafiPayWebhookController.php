<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WaafiPayWebhookController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handle WaafiPay payment confirmation webhook.
     */
    public function handlePaymentConfirmation(Request $request): JsonResponse
    {
        try {
            // Log the incoming webhook data
            Log::info('WaafiPay Webhook Received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all(),
            ]);

            // Validate webhook data
            $data = $request->validate([
                'referenceId' => 'required|string',
                'transactionId' => 'nullable|string',
                'responseCode' => 'required|string',
                'responseMsg' => 'nullable|string',
                'state' => 'nullable|string',
            ]);

            // Find the transaction by reference ID
            $transaction = PaymentTransaction::where('reference_id', $data['referenceId'])->first();

            if (!$transaction) {
                Log::warning('WaafiPay Webhook: Transaction not found', [
                    'reference_id' => $data['referenceId'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            // Check if transaction is already processed
            if ($transaction->isCompleted() || $transaction->isFailed()) {
                Log::info('WaafiPay Webhook: Transaction already processed', [
                    'reference_id' => $data['referenceId'],
                    'status' => $transaction->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction already processed',
                ], 200);
            }

            // Update transaction with webhook data
            $transaction->update([
                'transaction_id' => $data['transactionId'] ?? $transaction->transaction_id,
                'response_code' => $data['responseCode'],
                'response_message' => $data['responseMsg'] ?? null,
                'response_data' => $request->all(),
            ]);

            // Check response code to determine success/failure
            // WaafiPay response codes: 2001 = Success, others = Failed
            if ($data['responseCode'] === '2001' || $data['state'] === 'APPROVED') {
                // Mark transaction as completed
                $transaction->markAsCompleted($request->all());

                Log::info('WaafiPay Webhook: Payment successful', [
                    'reference_id' => $data['referenceId'],
                    'transaction_id' => $data['transactionId'] ?? null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'transaction_id' => $transaction->id,
                ], 200);
            } else {
                // Mark transaction as failed
                $errorMessage = $data['responseMsg'] ?? 'Payment failed';
                $transaction->markAsFailed($errorMessage, $request->all());

                Log::warning('WaafiPay Webhook: Payment failed', [
                    'reference_id' => $data['referenceId'],
                    'response_code' => $data['responseCode'],
                    'error_message' => $errorMessage,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error('WaafiPay Webhook Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing error',
            ], 500);
        }
    }

    /**
     * Check payment status (for polling).
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $referenceId = $request->input('reference_id');

            if (!$referenceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reference ID is required',
                ], 400);
            }

            $transaction = PaymentTransaction::where('reference_id', $referenceId)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'transaction_id' => $transaction->id,
                'transaction' => [
                    'id' => $transaction->id,
                    'reference_id' => $transaction->reference_id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'provider' => $transaction->provider,
                    'customer_name' => $transaction->customer_name,
                    'customer_phone' => $transaction->customer_phone,
                    'response_message' => $transaction->response_message,
                    'created_at' => $transaction->created_at,
                    'completed_at' => $transaction->completed_at,
                    'failed_at' => $transaction->failed_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Payment Status Check Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status',
            ], 500);
        }
    }
}

