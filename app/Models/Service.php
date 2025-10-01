<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUniqueSlug;
use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use HasUniqueSlug;
    use QueryBuilderTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'price',
        'cost',
        'category_id',
        'status',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profit_margin',
        'profit_percentage',
        'formatted_price',
    ];

    /**
     * Get the category that owns the service.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured services.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }



    /**
     * Scope a query to filter by category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search services by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the profit margin (price - cost).
     */
    public function getProfitMarginAttribute(): float
    {
        return $this->price - $this->cost;
    }

    /**
     * Get the profit percentage.
     */
    public function getProfitPercentageAttribute(): float
    {
        if ($this->cost == 0) {
            return 100.0;
        }

        return round((($this->price - $this->cost) / $this->cost) * 100, 2);
    }

    /**
     * Get the formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format((float) $this->price, 2);
    }

    /**
     * Get the status badge color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'yellow',
            'discontinued' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'discontinued' => 'Discontinued',
            default => 'Unknown',
        };
    }


}
