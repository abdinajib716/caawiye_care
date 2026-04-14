<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RevenueLedger extends Model
{
    use HasFactory;

    protected $table = 'revenue_ledger';

    protected $fillable = [
        'transaction_date',
        'order_id',
        'order_type',
        'service_type',
        'amount',
        'type',
        'description',
        'related_refund_id',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_amount',
        'type_label',
        'type_color',
    ];

    public function order(): MorphTo
    {
        return $this->morphTo('order', 'order_type', 'order_id');
    }

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class, 'related_refund_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeRevenue($query)
    {
        return $query->where('type', 'revenue');
    }

    public function scopeReversal($query)
    {
        return $query->where('type', 'reversal');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function isRevenue(): bool
    {
        return $this->type === 'revenue';
    }

    public function isReversal(): bool
    {
        return $this->type === 'reversal';
    }

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->isReversal() ? '-' : '';
        return $prefix . '$' . number_format(abs((float) $this->amount), 2);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'revenue' => 'Revenue',
            'reversal' => 'Reversal',
            default => 'Unknown',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'revenue' => 'green',
            'reversal' => 'red',
            default => 'gray',
        };
    }

    public static function recordRevenue(
        string $orderType,
        int $orderId,
        float $amount,
        string $serviceType,
        ?string $description = null,
        ?int $userId = null
    ): self {
        return self::create([
            'transaction_date' => now()->toDateString(),
            'order_type' => $orderType,
            'order_id' => $orderId,
            'service_type' => $serviceType,
            'amount' => $amount,
            'type' => 'revenue',
            'description' => $description ?? "Revenue from {$serviceType}",
            'created_by' => $userId ?? auth()->id(),
        ]);
    }

    public static function recordReversal(
        string $orderType,
        int $orderId,
        float $amount,
        string $serviceType,
        int $refundId,
        ?string $description = null,
        ?int $userId = null
    ): self {
        return self::create([
            'transaction_date' => now()->toDateString(),
            'order_type' => $orderType,
            'order_id' => $orderId,
            'service_type' => $serviceType,
            'amount' => -abs($amount),
            'type' => 'reversal',
            'description' => $description ?? "Revenue reversal for refund",
            'related_refund_id' => $refundId,
            'created_by' => $userId ?? auth()->id(),
        ]);
    }
}
