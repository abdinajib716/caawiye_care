<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUniqueSlug;
use App\Concerns\QueryBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;
    use HasUniqueSlug;
    use QueryBuilderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all services in this category.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Get active services in this category.
     */
    public function activeServices(): HasMany
    {
        return $this->services()->where('status', 'active');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full category path (for breadcrumbs).
     */
    public function getFullPathAttribute(): string
    {
        $path = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $path->implode(' > ');
    }

    /**
     * Get the services count for this category.
     */
    public function getServicesCountAttribute(): int
    {
        return $this->services()->count();
    }

    /**
     * Get the active services count for this category.
     */
    public function getActiveServicesCountAttribute(): int
    {
        return $this->activeServices()->count();
    }
}
