<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ReportCollection;
use App\Models\ReportCollectionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportCollectionService
{
    protected WaafipayService $waafipayService;

    public function __construct(WaafipayService $waafipayService)
    {
        $this->waafipayService = $waafipayService;
    }

    /**
     * Get default service charges from settings.
     */
    public function getServiceCharges(): array
    {
        return [
            'base_service_charge' => (float) get_setting('report_collection_service_charge', 2.00),
            'delivery_fee' => (float) get_setting('report_collection_delivery_fee', 1.00),
        ];
    }

    /**
     * Calculate total amount for a report collection request.
     */
    public function calculateTotal(bool $deliveryRequired, ?float $deliveryFee = null): array
    {
        $charges = $this->getServiceCharges();
        
        $serviceCharge = $charges['base_service_charge'];
        $resolvedDeliveryFee = $deliveryRequired ? ($deliveryFee ?? $charges['delivery_fee']) : 0;
        $totalAmount = $serviceCharge + $resolvedDeliveryFee;

        return [
            'service_charge' => $serviceCharge,
            'delivery_fee' => $resolvedDeliveryFee,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Process payment via Mobile Money API.
     */
    public function processPayment(array $data): array
    {
        try {
            $result = $this->waafipayService->processPayment([
                'phone' => $data['customer_phone'],
                'amount' => $data['total_amount'],
                'customer_name' => $data['customer_name'],
                'description' => 'Report Collection - ' . $data['patient_name'],
                'currency' => 'USD',
            ]);

            Log::info('Report Collection Payment result', [
                'success' => $result['success'] ?? null,
                'responseCode' => $result['response']['responseCode'] ?? null,
            ]);

            $responseCode = $result['response']['responseCode'] ?? null;
            
            if ($responseCode === '2001' && ($result['success'] ?? false)) {
                return [
                    'success' => true,
                    'reference' => $result['transaction']->reference_id ?? null,
                    'message' => 'Payment verified successfully',
                ];
            }

            return [
                'success' => false,
                'reference' => null,
                'message' => $result['response']['responseMsg'] ?? $result['message'] ?? 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('Report Collection Payment error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'reference' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new report collection request.
     */
    public function createRequest(array $data): ReportCollection
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $totals = $this->calculateTotal(
                $data['delivery_required'] ?? false,
                isset($data['delivery_fee']) ? (float) $data['delivery_fee'] : null
            );

            // Create the report collection
            $reportCollection = ReportCollection::create([
                'request_id' => ReportCollection::generateRequestId(),
                'medicine_order_id' => $data['medicine_order_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'patient_name' => $data['patient_name'],
                'patient_reference' => $data['patient_reference'] ?? null,
                'provider_type' => $data['provider_type'],
                'provider_name' => $data['provider_name'],
                'provider_address' => $data['provider_address'] ?? null,
                'delivery_required' => $data['delivery_required'] ?? false,
                'pickup_location_id' => $data['pickup_location_id'] ?? null,
                'dropoff_location_id' => $data['dropoff_location_id'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'delivery_time' => $data['delivery_time'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'assigned_staff_id' => $data['assigned_staff_id'],
                'assignment_notes' => $data['assignment_notes'] ?? null,
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_status' => $data['payment_reference'] ? ReportCollection::PAYMENT_VERIFIED : ReportCollection::PAYMENT_PENDING,
                'payment_verified_at' => $data['payment_reference'] ? now() : null,
                'service_charge' => $totals['service_charge'],
                'delivery_fee' => $totals['delivery_fee'],
                'total_amount' => $totals['total_amount'],
                'status' => ReportCollection::STATUS_PENDING,
                'created_by' => auth()->id(),
            ]);

            // Log creation
            $reportCollection->logs()->create([
                'action' => 'created',
                'new_value' => ReportCollection::STATUS_PENDING,
                'notes' => 'Request created with payment verification',
                'performed_by' => auth()->id(),
            ]);

            return $reportCollection;
        });
    }

    /**
     * Update request status to In Progress.
     */
    public function startProgress(ReportCollection $reportCollection, ?string $notes = null): bool
    {
        if (!$reportCollection->canTransitionTo(ReportCollection::STATUS_IN_PROGRESS)) {
            return false;
        }

        return $reportCollection->transitionTo(
            ReportCollection::STATUS_IN_PROGRESS,
            auth()->id(),
            $notes
        );
    }

    /**
     * Update request status to Completed.
     */
    public function markCompleted(ReportCollection $reportCollection, ?string $notes = null): bool
    {
        if (!$reportCollection->canTransitionTo(ReportCollection::STATUS_COMPLETED)) {
            return false;
        }

        return $reportCollection->transitionTo(
            ReportCollection::STATUS_COMPLETED,
            auth()->id(),
            $notes
        );
    }

    /**
     * Get statistics for dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'total' => ReportCollection::count(),
            'pending' => ReportCollection::pending()->count(),
            'in_progress' => ReportCollection::inProgress()->count(),
            'completed' => ReportCollection::completed()->count(),
            'total_revenue' => ReportCollection::paymentVerified()->sum('total_amount'),
            'today_count' => ReportCollection::whereDate('created_at', today())->count(),
            'today_revenue' => ReportCollection::whereDate('created_at', today())
                ->paymentVerified()
                ->sum('total_amount'),
        ];
    }

    /**
     * Get paginated list of report collections.
     */
    public function getList(array $filters = [], int $perPage = 15)
    {
        $query = ReportCollection::with(['assignedStaff', 'creator'])
            ->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['assigned_staff_id'])) {
            $query->where('assigned_staff_id', $filters['assigned_staff_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('request_id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }
}
