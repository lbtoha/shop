<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * The fulfilment pipeline (cancelled is a terminal off-ramp, not part of it).
     */
    public static function pipeline(): array
    {
        return [self::PENDING, self::CONFIRMED, self::PROCESSING, self::SHIPPED, self::DELIVERED];
    }

    /**
     * The next status in the fulfilment pipeline, or null if at the end / cancelled.
     */
    public function next(): ?self
    {
        $pipeline = self::pipeline();
        $index = array_search($this, $pipeline, true);

        if ($index === false || $index === count($pipeline) - 1) {
            return null;
        }

        return $pipeline[$index + 1];
    }

    public function isTerminal(): bool
    {
        return $this === self::DELIVERED || $this === self::CANCELLED;
    }
}
