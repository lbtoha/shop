<?php

namespace App\Models;

use App\Enums\MenuLocationEnum;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use \Cviebrock\EloquentSluggable\Sluggable;

    protected $guarded = [];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getSearchAttribute()
    {
        return [
            'name',
        ];
    }

    public function casts(): array
    {
        return [
            'location' => MenuLocationEnum::class,
        ];
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

    public function scopeActive($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'active');
    }
}
