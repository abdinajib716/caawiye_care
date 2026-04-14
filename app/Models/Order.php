<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use QueryBuilderTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'agent_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'provider_cost',
        'provider_id',
        'provider_type',
        'payment_method',
        'payment_provider',
        'payment_phone',
        'payment_status',
        'payment_transaction_id',
        'status',
        'notes',
        'completed_at',
        'revenue_recorded_at',
        'refunded_at',
        'refund_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'provider_cost' => 'decimal:2',
        'completed_at' => 'datetime',
        'revenue_recorded_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_total',
        'formatted_subtotal',
        'status_label',
        'status_color',
        'payment_status_label',
        'payment_status_color',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the agent (user) who created the order.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment transaction for the order.
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    /**
     * Get the provider for the order.
     */
    public function provider()
    {
        return $this->morphTo('provider', 'provider_type', 'provider_id');
    }

    /**
     * Get the provider payments for this order.
     */
    public function providerPayments()
    {
        return $this->morphMany(ProviderPayment::class, 'order', 'order_type', 'order_id');
    }

    /**
     * Get the refunds for this order.
     */
    public function refunds()
    {
        return $this->morphMany(Refund::class, 'order', 'order_type', 'order_id');
    }

    /**
     * Get the revenue ledger entries for this order.
     */
    public function revenueEntries()
    {
        return $this->morphMany(RevenueLedger::class, 'order', 'order_type', 'order_id');
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include failed orders.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to search orders by order number or customer name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the order is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark the order as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark the order as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the order as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Mark the order as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Get formatted total with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format((float) $this->total, 2);
    }

    /**
     * Get formatted subtotal with currency.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format((float) $this->subtotal, 2);
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed',
            default => 'Unknown',
        };
    }

    /**
     * Get the status badge color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'cancelled' => 'gray',
            'failed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the payment status label for display.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }

    /**
     * Get the payment status badge color for UI.
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Scope a query to only include refunded orders.
     */
    public function scopeRefunded($query)
    {
        return $query->whereNotNull('refunded_at');
    }

    /**
     * Scope a query to exclude refunded orders.
     */
    public function scopeNotRefunded($query)
    {
        return $query->whereNull('refunded_at');
    }

    /**
     * Check if the order is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->refunded_at !== null || $this->payment_status === 'refunded';
    }

    /**
     * Check if the order can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isCompleted() && !$this->isRefunded();
    }

    /**
     * Check if revenue has been recorded.
     */
    public function hasRevenueRecorded(): bool
    {
        return $this->revenue_recorded_at !== null;
    }

    /**
     * Mark the order as refunded.
     */
    public function markAsRefunded(string $reason = null): void
    {
        $this->update([
            'refunded_at' => now(),
            'refund_reason' => $reason,
            'payment_status' => 'refunded',
        ]);
    }

    /**
     * Get formatted provider cost with currency.
     */
    public function getFormattedProviderCostAttribute(): string
    {
        return '$' . number_format((float) $this->provider_cost, 2);
    }

    /**
     * Get the profit (total - provider_cost).
     */
    public function getProfitAttribute(): float
    {
        return (float) $this->total - (float) $this->provider_cost;
    }

    /**
     * Get formatted profit with currency.
     */
    public function getFormattedProfitAttribute(): string
    {
        return '$' . number_format($this->profit, 2);
    }

    /**
     * Get the order type class name for polymorphic relations.
     */
    public static function getOrderType(): string
    {
        return Order::class;
    }
}

