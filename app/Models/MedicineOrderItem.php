<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_order_id',
        'medicine_id',
        'medicine_name',
        'quantity',
        'cost',
        'profit',
        'profit_type',
        'profit_amount',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function medicineOrder(): BelongsTo
    {
        return $this->belongsTo(MedicineOrder::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
