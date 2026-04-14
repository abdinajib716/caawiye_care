<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function pickupPrices(): HasMany
    {
        return $this->hasMany(DeliveryPrice::class, 'pickup_location_id');
    }

    public function dropoffPrices(): HasMany
    {
        return $this->hasMany(DeliveryPrice::class, 'dropoff_location_id');
    }

    public function pickupOrders(): HasMany
    {
        return $this->hasMany(MedicineOrder::class, 'pickup_location_id');
    }

    public function dropoffOrders(): HasMany
    {
        return $this->hasMany(MedicineOrder::class, 'dropoff_location_id');
    }
}
