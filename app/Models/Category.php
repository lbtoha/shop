<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Sluggable;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('categories.is_active', true)
            ->where(function ($q) {
                $q->whereNull('categories.parent_id')
                  ->orWhereExists(function ($sub) {
                      $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('categories as parents')
                          ->whereColumn('parents.id', 'categories.parent_id')
                          ->where('parents.is_active', true);
                  });
            });
    }
}
