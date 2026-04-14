<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScanImagingService extends Model
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
        'service_name',
        'provider_id',
        'cost',
        'commission_percentage',
        'commission_type',
        'commission_amount',
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
    ];

    /**
     * Get the provider that owns the service.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive services.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to search services.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('service_name', 'like', "%{$search}%")
              ->orWhereHas('provider', function ($providerQuery) use ($search) {
                  $providerQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Check if the service is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the service is inactive.
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
     * Get the total with commission.
     */
    public function getTotalWithCommissionAttribute(): float
    {
        if ($this->commission_type === 'bill_customer') {
            return (float) $this->cost + (float) $this->commission_amount;
        }
        return (float) $this->cost;
    }

    /**
     * Get formatted commission amount.
     */
    public function getCommissionAmountFormattedAttribute(): string
    {
        return '$' . number_format((float) $this->commission_amount, 2);
    }

    /**
     * Get formatted total with commission.
     */
    public function getTotalWithCommissionFormattedAttribute(): string
    {
        return '$' . number_format($this->total_with_commission, 2);
    }

    /**
     * Get the service name attribute (alias for service_name).
     */
    public function getNameAttribute(): string
    {
        return $this->service_name;
    }
}
