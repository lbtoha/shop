<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'data' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TaskSchedule::class, 'schedule_id');
    }
}
