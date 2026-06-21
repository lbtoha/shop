<?php

namespace App\Models;

use App\Enums\HomeSectionLayoutEnum;
use App\Enums\HomeSectionSourceEnum;
use App\Repositories\HomeSectionRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeSection extends Model
{
    protected $guarded = [];

    protected $casts = [
        'source' => HomeSectionSourceEnum::class,
        'layout' => HomeSectionLayoutEnum::class,
        'product_ids' => 'array',
        'product_limit' => 'integer',
        'fallback_latest' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Flush the cached storefront payload whenever a section changes,
     * so admin edits show up immediately.
     */
    protected static function booted(): void
    {
        $flush = fn () => app(HomeSectionRepository::class)->flushCache();

        static::saved($flush);
        static::deleted($flush);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getSearchAttribute(): array
    {
        return ['title', 'subtitle'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
