<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTime extends Model
{
    protected $guarded = ['id'];

    public function getSearchAttribute()
    {
        return [
            'name',
        ];
    }
}
