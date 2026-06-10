<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use Sluggable;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getSearchAttribute(): array
    {
        return [
            'name',
            'slug',
            'sku',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        // A product is purchasable if its own stock is positive OR it has at
        // least one variant with stock. (Variant stock lives on a related table,
        // so use a subquery rather than a column comparison.)
        return $query->where(function ($q) {
            $q->where('stock', '>', 0)
                ->orWhereHas('variants', fn ($v) => $v->where('stock', '>', 0));
        });
    }

    /**
     * Whether this product sells through variants (size/color) rather than a
     * single stock pool.
     */
    public function hasVariants(): bool
    {
        // Prefer already-loaded data to avoid N+1 on listing pages.
        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }

        if (isset($this->attributes['variants_count'])) {
            return $this->variants_count > 0;
        }

        return $this->variants()->exists();
    }

    /**
     * The stock figure that actually governs purchasability: the sum of variant
     * stock when variants exist, otherwise the product's own stock.
     */
    public function effectiveStock(): int
    {
        if ($this->relationLoaded('variants') ? $this->variants->isNotEmpty() : $this->hasVariants()) {
            return (int) $this->variants()->sum('stock');
        }

        return (int) $this->stock;
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->effectiveStock() >= $quantity;
    }

    /**
     * Lowest sellable price including variant adjustments (used for "from" labels).
     */
    public function displayPrice(): float
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return (float) $this->price + (float) $this->variants->min('price_adjustment');
        }

        return (float) $this->price;
    }
}
