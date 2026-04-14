<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_location_id',
        'dropoff_location_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'pickup_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'dropoff_location_id');
    }
}
