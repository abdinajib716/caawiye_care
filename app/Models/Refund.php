<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use HasFactory;
    use QueryBuilderTrait;
    use SoftDeletes;

    protected $fillable = [
        'refund_number',
        'order_id',
        'order_type',
        'original_amount',
        'refund_amount',
        'reason',
        'status',
        'provider_payment_reversed',
        'provider_refund_confirmed_at',
        'refund_method',
        'refund_reference',
        'refund_executed_at',
        'revenue_reversal_id',
        'requested_by',
        'approved_by',
        'approved_at',
        'processed_by',
        'processed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'provider_payment_reversed' => 'boolean',
        'provider_refund_confirmed_at' => 'datetime',
        'refund_executed_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_refund_amount',
        'formatted_original_amount',
        'status_label',
        'status_color',
    ];

    public function order(): MorphTo
    {
        return $this->morphTo('order', 'order_type', 'order_id');
    }

    public function revenueReversal(): BelongsTo
    {
        return $this->belongsTo(RevenueLedger::class, 'revenue_reversal_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('refund_number', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%")
              ->orWhere('refund_reference', 'like', "%{$search}%");
        });
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canBeApproved(): bool
    {
        return $this->isPending() && $this->provider_payment_reversed;
    }

    public function canBeProcessed(): bool
    {
        return $this->isApproved();
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsProcessing(int $userId): void
    {
        $this->update([
            'status' => 'processing',
            'processed_by' => $userId,
            'processed_at' => now(),
        ]);
    }

    public function complete(string $method, ?string $reference = null): void
    {
        $this->update([
            'status' => 'completed',
            'refund_method' => $method,
            'refund_reference' => $reference,
            'refund_executed_at' => now(),
        ]);
    }

    public function confirmProviderPaymentReversed(): void
    {
        $this->update([
            'provider_payment_reversed' => true,
            'provider_refund_confirmed_at' => now(),
        ]);
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return '$' . number_format((float) $this->refund_amount, 2);
    }

    public function getFormattedOriginalAmountAttribute(): string
    {
        return '$' . number_format((float) $this->original_amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'processing' => 'orange',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
