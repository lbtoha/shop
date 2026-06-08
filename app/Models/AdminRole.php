<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'caps' => 'array',
        'module_caps' => 'array',
        'is_supper_admin' => 'boolean',
    ];

    protected $appends = ['caps_text'];

    public function users()
    {
        return $this->hasMany(Admin::class, 'admin_role_id', 'id');
    }

    public function getCapsTextAttribute($value)
    {
        if (empty($this->caps)) {
            return null;
        }

        return implode(',', array_map(fn ($cap) => config('caps'.$cap), $this->caps));
    }

    public function getSearchAttribute()
    {
        return [
            'name',
        ];
    }
}
