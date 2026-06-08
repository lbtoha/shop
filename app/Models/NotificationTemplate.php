<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'short_codes' => 'array',
    ];

    public function getSearchAttribute()
    {
        return [
            'name',
            'bodies.subject',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault($query)
    {
        return $query->where('type', 'default');
    }

    public function scopeNotDefault($query)
    {
        return $query->where('type', '!=', 'default');
    }

    public function bodies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(NotificationTemplateBody::class, 'notification_template_id');
    }

    public function emailBody()
    {
        return $this->bodies()->where('channel', 'email');
    }

    public function smsBody()
    {
        return $this->bodies()->where('channel', 'sms');
    }

    public function pushBody()
    {
        return $this->bodies()->where('channel', 'push');
    }
}
