<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCollection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'request_id',
        'medicine_order_id',
        'customer_name',
        'customer_phone',
        'patient_name',
        'patient_reference',
        'provider_type',
        'provider_name',
        'provider_address',
        'delivery_required',
        'pickup_location_id',
        'dropoff_location_id',
        'delivery_date',
        'delivery_time',
        'internal_notes',
        'assigned_staff_id',
        'assignment_notes',
        'payment_method',
        'payment_reference',
        'payment_status',
        'payment_verified_at',
        'service_charge',
        'delivery_fee',
        'total_amount',
        'status',
        'started_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'delivery_required' => 'boolean',
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i',
        'payment_verified_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'service_charge' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'payment_status_label',
        'payment_status_color',
        'provider_type_label',
        'payment_method_label',
    ];

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    // Payment status constants
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_VERIFIED = 'verified';
    public const PAYMENT_FAILED = 'failed';

    // Provider types
    public const PROVIDER_HOSPITAL = 'hospital';
    public const PROVIDER_LABORATORY = 'laboratory';
    public const PROVIDER_SUPPLIER = 'supplier';
    public const PROVIDER_OTHER = 'other';

    // Payment methods
    public const PAYMENT_EVC_PLUS = 'evc_plus';
    public const PAYMENT_E_DAHAB = 'e_dahab';

    /**
     * Get the assigned staff member.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function medicineOrder(): BelongsTo
    {
        return $this->belongsTo(MedicineOrder::class);
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'pickup_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'dropoff_location_id');
    }

    /**
     * Get the user who created the request.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the audit logs for this collection.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ReportCollectionLog::class);
    }

    /**
     * Generate a unique request ID.
     */
    public static function generateRequestId(): string
    {
        $prefix = 'RC';
        $date = now()->format('Ymd');
        $lastRequest = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRequest ? ((int) substr($lastRequest->request_id, -4)) + 1 : 1;

        return $prefix . $date . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if status can be changed to a new status.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            self::STATUS_PENDING => [self::STATUS_IN_PROGRESS],
            self::STATUS_IN_PROGRESS => [self::STATUS_COMPLETED],
            self::STATUS_COMPLETED => [], // Cannot transition from completed
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    /**
     * Transition to a new status.
     */
    public function transitionTo(string $newStatus, ?int $performedBy = null, ?string $notes = null): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;

        if ($newStatus === self::STATUS_IN_PROGRESS) {
            $this->started_at = now();
        } elseif ($newStatus === self::STATUS_COMPLETED) {
            $this->completed_at = now();
        }

        $this->save();

        // Log the status change
        $this->logs()->create([
            'action' => 'status_change',
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'notes' => $notes,
            'performed_by' => $performedBy ?? auth()->id(),
        ]);

        return true;
    }

    // Attribute accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_COMPLETED => __('Completed'),
            default => __('Unknown'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_COMPLETED => 'green',
            default => 'gray',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PENDING => __('Pending'),
            self::PAYMENT_VERIFIED => __('Verified'),
            self::PAYMENT_FAILED => __('Failed'),
            default => __('Unknown'),
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PENDING => 'yellow',
            self::PAYMENT_VERIFIED => 'green',
            self::PAYMENT_FAILED => 'red',
            default => 'gray',
        };
    }

    public function getProviderTypeLabelAttribute(): string
    {
        return match ($this->provider_type) {
            self::PROVIDER_HOSPITAL => __('Hospital'),
            self::PROVIDER_LABORATORY => __('Laboratory'),
            self::PROVIDER_SUPPLIER => __('Supplier'),
            self::PROVIDER_OTHER => __('Other'),
            default => __('Unknown'),
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_EVC_PLUS => __('EVC Plus'),
            self::PAYMENT_E_DAHAB => __('E-Dahab'),
            default => __('Unknown'),
        };
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for in progress requests.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for verified payments.
     */
    public function scopePaymentVerified($query)
    {
        return $query->where('payment_status', self::PAYMENT_VERIFIED);
    }
}
