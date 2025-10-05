<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Constructor with dependency injection.
     */
    public function __construct(
        private ServiceFieldDataService $fieldDataService,
        private AppointmentService $appointmentService
    ) {
    }

    /**
     * Get paginated orders with optional filters.
     */
    public function getPaginatedOrders(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::with(['customer', 'agent', 'items.service', 'paymentTransaction']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply payment status filter
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Apply payment method filter
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Apply customer filter
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Apply agent filter
        if (!empty($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new order with items.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Generate unique order number
            $data['order_number'] = $this->generateOrderNumber();

            // Set agent_id to current user if not provided
            if (empty($data['agent_id'])) {
                $data['agent_id'] = Auth::id();
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['unit_price'] * $item['quantity'];
            }

            $data['subtotal'] = $subtotal;
            $data['tax'] = $data['tax'] ?? 0;
            $data['discount'] = $data['discount'] ?? 0;
            $data['total'] = $subtotal + $data['tax'] - $data['discount'];

            // Create order
            $order = Order::create([
                'order_number' => $data['order_number'],
                'customer_id' => $data['customer_id'],
                'agent_id' => $data['agent_id'],
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'],
                'discount' => $data['discount'],
                'total' => $data['total'],
                'payment_method' => $data['payment_method'],
                'payment_provider' => $data['payment_provider'] ?? null,
                'payment_phone' => $data['payment_phone'],
                'payment_status' => $data['payment_status'] ?? 'pending',
                'payment_transaction_id' => $data['payment_transaction_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order items
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            return $order->load(['customer', 'agent', 'items.service']);
        });
    }

    /**
     * Create order from completed payment transaction.
     * This is called AFTER payment is confirmed.
     */
    public function createOrderFromTransaction(\App\Models\PaymentTransaction $transaction, array $orderData): Order
    {
        return DB::transaction(function () use ($transaction, $orderData) {
            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();

            // Set agent_id to current user if not provided
            $agentId = $orderData['agent_id'] ?? Auth::id();

            // Calculate totals
            $subtotal = 0;
            foreach ($orderData['items'] as $item) {
                $subtotal += $item['unit_price'] * $item['quantity'];
            }

            $tax = $orderData['tax'] ?? 0;
            $discount = $orderData['discount'] ?? 0;
            $total = $subtotal + $tax - $discount;

            // Create order with completed payment status
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $orderData['customer_id'],
                'agent_id' => $agentId,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $orderData['payment_method'],
                'payment_provider' => $orderData['payment_provider'] ?? null,
                'payment_phone' => $orderData['payment_phone'],
                'payment_status' => 'completed', // Payment already completed
                'payment_transaction_id' => $transaction->id,
                'status' => 'completed', // Mark as completed since payment is done
                'completed_at' => now(), // Set completion timestamp
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Create order items
            foreach ($orderData['items'] as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);

                // Handle service field data if present
                if (!empty($item['field_data'])) {
                    $service = Service::find($item['service_id']);
                    if ($service && $service->hasCustomFields()) {
                        $this->fieldDataService->saveFieldData($orderItem, $service, $item['field_data']);

                        // Create appointment if service is appointment type
                        if ($service->isAppointment()) {
                            $this->createAppointmentFromFieldData(
                                $order,
                                $orderItem,
                                $item['field_data']
                            );
                        }
                    }
                }
            }

            return $order->load(['customer', 'agent', 'items.service', 'paymentTransaction']);
        });
    }

    /**
     * Create appointment from field data.
     */
    private function createAppointmentFromFieldData(Order $order, OrderItem $orderItem, array $fieldData): void
    {
        $appointmentData = [
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'customer_id' => $order->customer_id,
            'hospital_id' => $fieldData['hospital_id'],
            'appointment_type' => $fieldData['appointment_type'] ?? 'self',
            'patient_name' => $fieldData['patient_name'] ?? null,
            'appointment_time' => $fieldData['appointment_time'],
            'status' => 'scheduled',
        ];

        $this->appointmentService->createAppointment($appointmentData);
    }

    /**
     * Update an existing order.
     */
    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            // Update order
            $order->update([
                'status' => $data['status'] ?? $order->status,
                'payment_status' => $data['payment_status'] ?? $order->payment_status,
                'notes' => $data['notes'] ?? $order->notes,
            ]);

            // If status is completed, set completed_at
            if ($data['status'] === 'completed' && !$order->completed_at) {
                $order->update(['completed_at' => now()]);
            }

            return $order->fresh(['customer', 'agent', 'items.service']);
        });
    }

    /**
     * Delete an order (soft delete).
     */
    public function deleteOrder(Order $order): bool
    {
        return $order->delete();
    }

    /**
     * Get order statistics.
     */
    public function getOrderStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::pending()->count(),
            'processing_orders' => Order::processing()->count(),
            'completed_orders' => Order::completed()->count(),
            'cancelled_orders' => Order::cancelled()->count(),
            'failed_orders' => Order::failed()->count(),
            'total_revenue' => (float) Order::completed()->sum('total'),
            'average_order_value' => (float) Order::completed()->avg('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => (float) Order::whereDate('created_at', today())->where('status', 'completed')->sum('total'),
        ];
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with(['customer', 'agent', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get orders by customer.
     */
    public function getOrdersByCustomer(Customer $customer): Collection
    {
        return $customer->orders()
            ->with(['agent', 'items.service'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get customer order statistics.
     */
    public function getCustomerOrderStatistics(Customer $customer): array
    {
        return [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->completed()->count(),
            'total_spent' => (float) $customer->orders()->completed()->sum('total'),
            'average_order_value' => (float) $customer->orders()->completed()->avg('total'),
            'last_order_date' => $customer->orders()->latest()->first()?->created_at,
        ];
    }

    /**
     * Generate unique order number.
     */
    public function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
        
        $orderNumber = "{$prefix}-{$date}-{$random}";

        // Ensure uniqueness
        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 6));
            $orderNumber = "{$prefix}-{$date}-{$random}";
        }

        return $orderNumber;
    }

    /**
     * Bulk update order status.
     */
    public function bulkUpdateStatus(array $orderIds, string $status): int
    {
        return Order::whereIn('id', $orderIds)
            ->update(['status' => $status]);
    }

    /**
     * Bulk delete orders.
     */
    public function bulkDelete(array $orderIds): int
    {
        return Order::whereIn('id', $orderIds)->delete();
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Order $order, string $reason = null): Order
    {
        $order->markAsCancelled();
        
        if ($reason) {
            $order->update(['notes' => $order->notes . "\n\nCancellation Reason: " . $reason]);
        }

        return $order->fresh();
    }

    /**
     * Get order by order number.
     */
    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)
            ->with(['customer', 'agent', 'items.service', 'paymentTransaction'])
            ->first();
    }
}

