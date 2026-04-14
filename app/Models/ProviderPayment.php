<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderPayment extends Model
{
    use HasFactory;
    use QueryBuilderTrait;
    use SoftDeletes;

    protected $fillable = [
        'payment_number',
        'order_id',
        'order_type',
        'provider_id',
        'provider_type',
        'amount',
        'status',
        'payment_method',
        'payment_reference',
        'expense_id',
        'approved_at',
        'approved_by',
        'paid_at',
        'paid_by',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_amount',
        'status_label',
        'status_color',
    ];

    public function order(): MorphTo
    {
        return $this->morphTo('order', 'order_type', 'order_id');
    }

    public function provider(): MorphTo
    {
        return $this->morphTo('provider', 'provider_type', 'provider_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('payment_number', 'like', "%{$search}%")
              ->orWhere('payment_reference', 'like', "%{$search}%");
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

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function canBeReversed(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function isNotPaidOrReversed(): bool
    {
        return !$this->isPaid() || $this->isReversed();
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(int $userId, string $method, ?string $reference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_by' => $userId,
            'paid_at' => now(),
            'payment_method' => $method,
            'payment_reference' => $reference,
        ]);
    }

    public function reverse(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'reversed',
            'reversed_by' => $userId,
            'reversed_at' => now(),
            'reversal_reason' => $reason,
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float) $this->amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'reversed' => 'Reversed',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'paid' => 'green',
            'reversed' => 'red',
            default => 'gray',
        };
    }
}
