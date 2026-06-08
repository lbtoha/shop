<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplateBody extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
