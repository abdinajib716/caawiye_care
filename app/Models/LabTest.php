<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTest extends Model
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
        'name',
        'provider_id',
        'cost',
        'commission_percentage',
        'commission_type',
        'commission_amount',
        'profit',
        'total',
        'status',
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
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'status_color',
        'commission_amount_formatted',
        'cost_formatted',
        'total_with_commission_formatted',
        'provider_payment_formatted',
    ];

    /**
     * Get the provider that owns the lab test.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Scope a query to only include active lab tests.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive lab tests.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to search lab tests.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('provider', function ($providerQuery) use ($search) {
                  $providerQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Check if the lab test is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the lab test is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            default => 'Unknown',
        };
    }

    /**
     * Get the status badge color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get formatted commission amount.
     */
    public function getCommissionAmountFormattedAttribute(): string
    {
        return '$' . number_format((float)$this->commission_amount, 2);
    }

    /**
     * Get formatted cost.
     */
    public function getCostFormattedAttribute(): string
    {
        return '$' . number_format((float)$this->cost, 2);
    }

    /**
     * Get formatted total with commission.
     */
    public function getTotalWithCommissionFormattedAttribute(): string
    {
        $total = (float)$this->cost + (float)$this->commission_amount;
        return '$' . number_format($total, 2);
    }

    /**
     * Get formatted provider payment.
     */
    public function getProviderPaymentFormattedAttribute(): string
    {
        $payment = (float)$this->cost - (float)$this->commission_amount;
        return '$' . number_format($payment, 2);
    }
}
