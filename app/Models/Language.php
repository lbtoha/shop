<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $guarded = [];

    private static $defaultLanguage;

    public function getSearchAttribute()
    {
        return [
            'name',
            'code',
        ];
    }

    public function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public static function getDefaultLanguage()
    {
        if (! self::$defaultLanguage) {
            self::$defaultLanguage = self::query()->where('is_default', true)->first();
        }

        return self::$defaultLanguage;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
