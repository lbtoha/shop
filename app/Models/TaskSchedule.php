<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSchedule extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'next_run' => 'datetime',
        'last_run' => 'datetime',
    ];

    public function getSearchAttribute()
    {
        return [
            'name',
        ];
    }

    public function schedule_time(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ScheduleTime::class);
    }

    public function schedule_logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScheduleLog::class, 'schedule_id');
    }
}
