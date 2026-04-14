<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScanImagingBooking extends Model
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
        'booking_number',
        'customer_id',
        'patient_name',
        'scan_imaging_service_id',
        'provider_id',
        'service_name',
        'provider_name',
        'cost',
        'commission_percentage',
        'commission_type',
        'commission_amount',
        'total',
        'appointment_time',
        'payment_status',
        'payment_method',
        'payment_reference',
        'status',
        'provider_payment_status',
        'notes',
        'cancellation_reason',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
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
        'total' => 'decimal:2',
        'appointment_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'status_color',
        'payment_status_label',
        'payment_status_color',
        'formatted_appointment_time',
    ];

    /**
     * Get the customer that owns the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the scan imaging service for the booking.
     */
    public function scanImagingService(): BelongsTo
    {
        return $this->belongsTo(ScanImagingService::class);
    }

    /**
     * Get the agent (user) who created the booking.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the provider for the booking.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include in-progress bookings.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to search bookings.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('booking_number', 'like', "%{$search}%")
              ->orWhere('patient_name', 'like', "%{$search}%")
              ->orWhere('service_name', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Mark the booking as confirmed.
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark the booking as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    /**
     * Mark the booking as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the booking as cancelled.
     */
    public function markAsCancelled(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
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
            'confirmed' => 'blue',
            'in_progress' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
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
            'paid' => 'Paid',
            'failed' => 'Failed',
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
            'paid' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get formatted appointment time.
     */
    public function getFormattedAppointmentTimeAttribute(): string
    {
        return $this->appointment_time->format('M d, Y h:i A');
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
     * Get formatted total amount.
     */
    public function getTotalAmountFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->total, 2);
    }

    /**
     * Get formatted profit (commission is the profit).
     */
    public function getProfitFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->commission_amount, 2);
    }

    /**
     * Get the scan imaging service relationship.
     */
    public function service()
    {
        return $this->belongsTo(ScanImagingService::class, 'scan_imaging_service_id');
    }
}
