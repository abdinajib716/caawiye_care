<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestBookingItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lab_test_booking_id',
        'lab_test_id',
        'provider_id',
        'test_name',
        'provider_name',
        'cost',
        'commission_percentage',
        'commission_type',
        'commission_amount',
        'profit',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'profit' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the lab test booking that owns the item.
     */
    public function labTestBooking(): BelongsTo
    {
        return $this->belongsTo(LabTestBooking::class);
    }

    /**
     * Get the lab test that owns the item.
     */
    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LabTest::class);
    }

    /**
     * Get the provider for the item.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get formatted cost.
     */
    public function getCostFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->cost, 2);
    }

    /**
     * Get formatted commission amount.
     */
    public function getCommissionAmountFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->commission_amount, 2);
    }

    /**
     * Get formatted subtotal (total).
     */
    public function getSubtotalFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->total, 2);
    }
}
