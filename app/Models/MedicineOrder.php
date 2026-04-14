<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'supplier_id',
        'agent_id',
        'requires_delivery',
        'pickup_location_id',
        'dropoff_location_id',
        'delivery_price',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_phone',
        'payment_status',
        'payment_reference',
        'status',
    ];

    protected $casts = [
        'requires_delivery' => 'boolean',
        'delivery_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicineOrderItem::class);
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'pickup_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'dropoff_location_id');
    }

    public function reportCollections(): HasMany
    {
        return $this->hasMany(ReportCollection::class);
    }
}
