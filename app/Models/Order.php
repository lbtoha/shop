<?php

namespace App\Models;

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'payment_status' => OrderPaymentStatusEnum::class,
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $appends = [
        'status_name',
        'payment_status_name',
    ];

    public function getSearchAttribute(): array
    {
        return [
            'order_number',
            'customer_name',
            'customer_phone',
            'customer_email',
        ];
    }

    protected function statusName(): Attribute
    {
        return Attribute::get(fn () => $this->status?->label());
    }

    protected function paymentStatusName(): Attribute
    {
        return Attribute::get(fn () => $this->payment_status?->label());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
