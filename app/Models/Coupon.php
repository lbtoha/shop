<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function getSearchAttribute(): array
    {
        return ['code'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Look up a usable coupon by code (case-insensitive). Returns null when the
     * code doesn't exist — eligibility against a specific cart is checked by
     * isRedeemable()/discountFor().
     */
    public static function findByCode(string $code): ?self
    {
        return static::query()->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))])->first();
    }

    /**
     * Whether the coupon can be applied to a cart of the given subtotal right now.
     * Sets $reason (translated) when it cannot.
     */
    public function isRedeemable(float $subtotal, ?string &$reason = null): bool
    {
        if (! $this->is_active) {
            $reason = __('This coupon is no longer active.');

            return false;
        }

        $today = now()->startOfDay();

        if ($this->starts_at && $today->lt($this->starts_at)) {
            $reason = __('This coupon is not active yet.');

            return false;
        }

        if ($this->expires_at && $today->gt($this->expires_at)) {
            $reason = __('This coupon has expired.');

            return false;
        }

        if (! is_null($this->usage_limit) && $this->used_count >= $this->usage_limit) {
            $reason = __('This coupon has reached its usage limit.');

            return false;
        }

        if ($subtotal < (float) $this->min_subtotal) {
            $reason = __('Add :amount more to use this coupon.', [
                'amount' => amountWithSymbol((float) $this->min_subtotal - $subtotal),
            ]);

            return false;
        }

        return true;
    }

    /**
     * The discount amount for a given subtotal (never exceeds the subtotal).
     * Returns 0 when the coupon is not redeemable.
     */
    public function discountFor(float $subtotal): float
    {
        if (! $this->isRedeemable($subtotal)) {
            return 0;
        }

        if ($this->type === 'percent') {
            $discount = $subtotal * ((float) $this->value / 100);

            if (! is_null($this->max_discount) && (float) $this->max_discount > 0) {
                $discount = min($discount, (float) $this->max_discount);
            }
        } else {
            $discount = (float) $this->value;
        }

        return round(min($discount, $subtotal), 2);
    }

    /**
     * Human label for the discount, e.g. "20%" or "$10".
     */
    public function valueLabel(): string
    {
        return $this->type === 'percent'
            ? rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.').'%'
            : amountWithSymbol((float) $this->value);
    }
}
